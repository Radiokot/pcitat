# <img src="https://github.com/Radiokot/pcitat-vue/raw/master/static/icon/apple-touch-icon.png" alt="Icon" style="vertical-align: bottom; height: 1.2em;"/> [Просто цитатник](https://pc.radiokot.com.ua)

Хранилище для цитат из книг на PHP + SQLite.

### Как запустить у себя? ###


* Стать на коммит ```41aba96``` (сейчас от первоначальной версии остался только API)
* Создать БД из заглушки
```
#!bash
cd php
cp quoter-scratch.sqlite .quoter.sqlite 
chmod 666 .quoter.sqlite
```
* Настроить веб-сервер для игнорирования расширения .php (/login = /login.php)
