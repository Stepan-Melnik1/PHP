# Лабораторная работа №1. HTTP

## Цель работы
Понять, что происходит, когда пользователь открывает сайт. Научиться находить и анализировать HTTP-запросы в браузере. Разобраться в назначении методов GET, POST, PUT, DELETE.

## Задание 1. Анализ HTTP-запросов.
### 1.1 Анализ запроса https://en.wikipedia.org/wiki/HTTP

- URL запроса: https://en.wikipedia.org/wiki/HTTP
- Метод запроса: GET
GET используется для получения ресурса с сервера. Мы не отправляем "Википедии" новые данные и не изменяем страницу — просто хотим получить её содержимое.
- Статус ответа: 200 OK
Это означает, что сервер успешно обработал запрос и вернул запрошенный ресурс.
<img width="418" height="135" alt="Снимок экрана_20260910_174241" src="https://github.com/user-attachments/assets/a2f18d1b-327b-4624-80bb-bb527a9c71d9" />

- Заголовки (Headers):
  - Заголовки запроса (Request Headers):
```
authority: en.wikipedia.org
method: GET
path: /wiki/HTTP
scheme: https
accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7
accept-encoding: gzip, deflate, br, zstd
accept-language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7
cache-control: max-age=0
cookie: WMF-Last-Access=10-Sep-2026; WMF-Last-Access-Global=10-Sep-2026; GeoIP=MD:CU:Chisinau:47.00:28.86:v4; WMF-Uniq=auTfBrv4hROc5bcDLJcUhgPXAAEBAFvdZr-0viKmadV_uwPv373qq5dUBJ5Y5HZr; enwikimwuser-sessionId=2a37c7cd1273753dc08a; WMF-DP=a14,606
priority: u=0, i
referer: https://elearning.usm.md/
sec-ch-ua: "Chromium";v="152", "Not?A_Brand";v="24", "Google Chrome";v="152"
sec-ch-ua-mobile: ?0
sec-ch-ua-platform: "Linux"
sec-fetch-dest: document
sec-fetch-mode: navigate
sec-fetch-site: cross-site
sec-fetch-user: ?1
upgrade-insecure-requests: 1
user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36
```

`:authority` - содержит имя сервера, к которому обращается браузер.
`:method` - указывает HTTP-метод запроса. У нас GET, то есть браузер запрашивает ресурс.
`:path` - указывает путь к ресурсу: /wiki/HTTP
`:scheme` - показывает используемый протокол: https
`:` в начале этих четырёх полей означает, что это псевдозаголовки HTTP/2, а не обычные HTTP-заголовки.
`Accept` - показывает, какие форматы содержимого браузер готов принять от сервера. В моем случае браузер сообщает, что может принимать HTML, XML, изображения и другие форматы.
`Accept-Encoding` - показывает, какие способы сжатия поддерживает браузер: gzip, deflate, br, zstd. Это позволяет серверу выбрать подходящий способ сжатия ответа.
`Accept-Language` - показывает предпочтительные языки пользователя. В моем случае указаны русский и английский языки.
`Cache-Control` - содержит инструкции, связанные с кэшированием запроса.
`Cookie` - передаёт серверу сохранённые браузером cookies, которые могут использоваться для хранения состояния пользователя, настроек и другой информации.
`Referer` - показывает страницу, с которой пользователь перешёл на текущую страницу.
`User-Agent` - очень важный заголовок. Он сообщает серверу информацию о клиенте — в моём случае браузере и операционной системе. У меня это Chrome на Linux.
`Sec-Fetch-*` - эти заголовки дают серверу дополнительную информацию о характере запроса: что запрашивается, каким способом выполняется переход и является ли запрос навигацией.
`Upgrade-Insecure-Requests` - сообщает серверу, что браузер предпочитает защищённое HTTPS-соединение.










