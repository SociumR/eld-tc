

[Встановлюємо Phalcon | https://docs.phalconphp.com/3.4/ru-ru/installation.html]
[Налаштовуємо Nginx | https://docs.phalconphp.com/3.4/ru-ru/webserver-setup.html]

> Створюємо файл локальних налаштувань

```
cp app/config/config.php $(pwd)/app/config/config-local.php
```

> Налаштувати app/config-local.php згідно коментарів в файлі

> Встановлюємо composer залежності
```
composer install
```

клонируем репозиторий

```bash
git clone https://gitlab.iwis.com.ua/d.novotorov/best-music-backend.git
```

> Ініціалізуємо проект
 - Відкриваємо app/config-local.php
 - Шукаємо:
```
 ...
    'whiteListMethods' => [
        '/admin/login'
    ],
 ...
 
```
  - Додаємо параметри
```
 
  ...
     'whiteListMethods' => [
         '/admin/login',
         '/admin/users'
     ],
  ...

```

> Example:
  ```
  curl -X POST "http://{ENDPOINT}/admin/users" -H "accept: application/json"
  ```
- Створюємо користувача
```
php console/index.php admin createUser [email] [password] [username] [role] [API URL]/admin/users
```
> Example:
```
php console/index.php admin createUser dmitriy.novotoro4v@gmail.com password d.novotorov4 admin http://{ENDPOINT}/admin/users
```

 - Відкриваємо app/config-local.php
 - Шукаємо:
```
 ...
    'whiteListMethods' => [
        '/admin/login',
        '/system/docs',
        '/system/init',
        '/admin/users'
    ],
 ...
 
```
  - Видаляємо параметри
```
 
  ...
     'whiteListMethods' => [
         '/admin/login',
         '/system/docs'
     ],
  ...
```
 ##Api Examples
 #####Створення
```
Method: POST /songs

Body:
    {
    	"title": "1",
    	"author": "1",
    }
   
```
```
Method: PUT /songs{id}

Body:
    {
    	"title": "1",
    	"author": "1",
    }
   
```
```
Method: DELETE /songs{id}

```
```

#####Список

Method: GET /sorts
  = /sorts?title=REPORT&author=2&
  SORT ASC /sorts?sort=title:ASC
  SORT ASC /sorts?sort=title:DESC
  IN /sorts?title="IN[1111]"
  PAGE LIMIT /sorts?pageLimit=10
```


