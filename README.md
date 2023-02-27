<h3>Books API</h3>

<p>Представляю вашему вниманию АПИ для работы с сущностями жанра, автора и книги. В приложении можно авторизоваться в качестве как админа, так и клиента. Админу доступны операции создания, изменения и удаления записей для этих сущностей, для клиента только просмотр активных записей. Так же предусмотрен поиск книг по названию, имени автора, стране автора и жанру. Для авторизации пользователей по JWT токенам используется библиотека JWTTools.
<p>

<h6>Для запуска приложения:</h6>
<ol>
<li>Склонируйте репозиторий в желаемую папку проекта</li>
<li>Загрузите зависимости командой composer install</li>
<li>Поднимите окружение командой docker-compose up</li>
<li>Выполните команду php init</li>
<li>В конфигах приложения (/common/config/main-local.php) замените настройки компонента db на:
<pre>'db' => [
            'class' => \yii\db\Connection::class,
            'dsn' => 'mysql:host=db;dbname=test',
            'username' => 'tester',
            'password' => 'testing',
            'charset' => 'utf8',
        ],
</pre
</li>
<li>Примените миграции командой php yii migrate</li>
<li>Импортируйте в Postman коллекцию заппросов из файла Books API.postman_collection.json</li>
</ol>

<h6>Реквизиты для входа в тестовые аккаунты:</h6>

<p>Администратор - username: admin, password: 1234</p>
<p>Клиент - username: client, password: 5678</p>
