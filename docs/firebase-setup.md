# Настройка Firebase Cloud Messaging (Push-уведомления)

## 1. Создание проекта Firebase

1. Перейди на https://console.firebase.google.com/
2. Нажми **Create a project** (или выбери существующий)
3. Введи имя проекта, следуй шагам мастера
4. Дождись создания проекта

## 2. Создание Web-приложения

1. В Firebase Console открой свой проект
2. На главной странице проекта нажми иконку **Web** (`</>`) — "Add app"
3. Введи название приложения (например, "Blog")
4. Нажми **Register app**
5. Появится блок с конфигурацией:

```javascript
const firebaseConfig = {
  apiKey: "AIzaSy...",           // -> FIREBASE_API_KEY
  authDomain: "xxx.firebaseapp.com",  // -> FIREBASE_AUTH_DOMAIN
  projectId: "xxx",             // -> FIREBASE_PROJECT_ID
  storageBucket: "xxx.firebasestorage.app", // -> FIREBASE_STORAGE_BUCKET
  messagingSenderId: "123456",  // -> FIREBASE_MESSAGING_SENDER_ID
  appId: "1:123456:web:abc..."  // -> FIREBASE_APP_ID
};
```

6. Скопируй каждое значение в `.env` файл (см. раздел 5)

Если приложение уже создано:
- **Project Settings** (шестеренка вверху слева) -> **General** -> прокрути вниз до **Your apps** -> выбери web-приложение

## 3. Получение VAPID Key

1. **Project Settings** (шестеренка) -> **Cloud Messaging**
2. Прокрути вниз до раздела **Web configuration**
3. В блоке **Web Push certificates**:
   - Если ключ уже есть — скопируй значение из колонки **Key pair**
   - Если нет — нажми **Generate key pair**
4. Скопируй полученный ключ — это `FIREBASE_VAPID_KEY`

## 4. Получение Service Account (серверный ключ)

Этот файл нужен серверу для отправки push-уведомлений через FCM API.

1. **Project Settings** (шестеренка) -> **Service accounts**
2. Убедись, что выбран **Firebase Admin SDK**
3. Нажми **Generate new private key**
4. Подтверди генерацию
5. Скачается JSON-файл (например, `forumbase-net-firebase-adminsdk-xxxxx.json`)
6. Переименуй и положи на сервер:

```bash
cp скачанный-файл.json storage/app/private/firebase-service-account.json
```

**ВАЖНО:** Этот файл содержит приватный ключ. Не коммить его в git. Убедись, что `storage/app/private/` есть в `.gitignore`.

## 5. Заполнение .env

```env
# Из раздела 2 (Web app config)
FIREBASE_API_KEY=AIzaSy...
FIREBASE_AUTH_DOMAIN=your-project.firebaseapp.com
FIREBASE_PROJECT_ID=your-project
FIREBASE_STORAGE_BUCKET=your-project.firebasestorage.app
FIREBASE_MESSAGING_SENDER_ID=123456789
FIREBASE_APP_ID=1:123456789:web:abcdef123456

# Из раздела 3 (VAPID key)
FIREBASE_VAPID_KEY=BKx7...длинная_строка...

# Из раздела 4 (путь к файлу сервисного аккаунта, относительно storage/app/private/)
FIREBASE_CREDENTIALS_PATH=firebase-service-account.json
```

## 6. После заполнения .env на проде

```bash
# Очистить кэш конфига чтобы новые значения подхватились
php artisan config:cache

# Очистить кэш view на всякий случай
php artisan view:clear
```

## 7. Проверка

### Проверка подписки:
1. Открой сайт в браузере
2. Должна появиться плашка с запросом разрешения на уведомления
3. Нажми "Разрешить"
4. В консоли браузера (F12) должно быть: `Push notification token obtained and saved`
5. В админке (Filament) -> **Push Subscriptions** — должна появиться новая запись

### Проверка отправки:
1. В админке -> **Push Subscriptions**
2. Нажми **Send** напротив подписки
3. Заполни заголовок и текст
4. Должно прийти push-уведомление в браузер

## Структура файлов

| Что | Где |
|-----|-----|
| `.env` переменные | Корень проекта, файл `.env` |
| Service Account JSON | `storage/app/private/firebase-service-account.json` |
| Service Worker (frontend) | `public/firebase-messaging-sw.js` |
| Frontend скрипт | `resources/views/layouts/app.blade.php` |
| Бэкенд сервис отправки | `app/Services/FirebaseService.php` |
| Конфиг Laravel | `config/services.php` (секция `firebase`) |
| Эндпоинт конфига для SW | `app/Http/Controllers/FirebaseConfigController.php` |
| API подписок | `app/Http/Controllers/Api/PushSubscriptionController.php` |
| Админка подписок | `app/Filament/Resources/PushSubscriptions/` |

## Частые проблемы

**Плашка подписки не появляется:**
- Проверь что сайт работает по HTTPS
- Открой консоль браузера (F12) — ищи ошибки Firebase/ServiceWorker
- Проверь что все `FIREBASE_*` переменные заполнены в `.env`
- После изменения `.env` выполни `php artisan config:cache`

**Ошибка "Service account file not found":**
- Файл `firebase-service-account.json` должен лежать в `storage/app/private/`
- Проверь: `ls -la storage/app/private/firebase-service-account.json`

**Ошибка отправки "401 Unauthorized":**
- Service Account JSON невалидный или от другого проекта
- Перегенерируй ключ (раздел 4) и замени файл

**После config:cache ничего не работает:**
- Убедись что в коде используется `config()`, а не `env()`
- Выполни `php artisan config:cache` повторно после изменений в `.env`
