# [Просто цитатник](https://pc.radiokot.com.ua)#

Хранилище для цитат из книг на PHP + SQLite.

### Как запустить у себя? ###


* Создать БД из заглушки
```
#!bash
cd php
cp quoter-scratch.sqlite quoter.sqlite 
chmod 666 quoter.sqlite
```
* Настроить веб-сервер для игнорирования расширения .php (/login = /login.php)