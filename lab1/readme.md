# Лабораторная работа №1. HTTP

## Цель работы
Понять, что происходит, когда пользователь открывает сайт. Научиться находить и анализировать HTTP-запросы в браузере. Разобраться в назначении методов GET, POST, PUT, DELETE.

## Задание 1. Анализ HTTP-запросов.
### 1.1 Анализ запроса https://en.wikipedia.org/wiki/HTTP

- **URL запроса:** https://en.wikipedia.org/wiki/HTTP
- **Метод запроса:** GET
GET используется для получения ресурса с сервера. Мы не отправляем "Википедии" новые данные и не изменяем страницу — просто хотим получить её содержимое.
- **Статус ответа:** 200 OK
Это означает, что сервер успешно обработал запрос и вернул запрошенный ресурс.
<img width="418" height="135" alt="Снимок экрана_20260910_174241" src="https://github.com/user-attachments/assets/a2f18d1b-327b-4624-80bb-bb527a9c71d9" />

- **Заголовки (Headers):**
  - **Заголовки запроса (Request Headers):**
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
```
* `:authority` - содержит имя сервера, к которому обращается браузер.
* `:method` - указывает HTTP-метод запроса. У нас GET, то есть браузер запрашивает ресурс.
* `:path` - указывает путь к ресурсу: /wiki/HTTP
* `:scheme` - показывает используемый протокол: https
* `:` в начале этих четырёх полей означает, что это псевдозаголовки HTTP/2, а не обычные HTTP-заголовки.
* `Accept` - показывает, какие форматы содержимого браузер готов принять от сервера. В моем случае браузер сообщает, что может принимать HTML, XML, изображения и другие форматы.
* `Accept-Encoding` - показывает, какие способы сжатия поддерживает браузер: gzip, deflate, br, zstd. Это позволяет серверу выбрать подходящий способ сжатия ответа.
* `Accept-Language` - показывает предпочтительные языки пользователя. В моем случае указаны русский и английский языки.
* `Cache-Control` - содержит инструкции, связанные с кэшированием запроса.
* `Cookie` - передаёт серверу сохранённые браузером cookies, которые могут использоваться для хранения состояния пользователя, настроек и другой информации.
* `Referer` - показывает страницу, с которой пользователь перешёл на текущую страницу.
* `User-Agent` - очень важный заголовок. Он сообщает серверу информацию о клиенте — в моём случае браузере и операционной системе. У меня это Chrome на Linux.
* `Sec-Fetch-*` - эти заголовки дают серверу дополнительную информацию о характере запроса: что запрашивается, каким способом выполняется переход и является ли запрос навигацией.
* `Upgrade-Insecure-Requests` - сообщает серверу, что браузер предпочитает защищённое HTTPS-соединение.
```
- **Заголовки ответа (Response Headers):**
```
accept-ch:
accept-ranges: bytes
age: 1153
cache-control: private, s-maxage=0, max-age=0, must-revalidate, no-transform
content-encoding: gzip
content-language: en
content-length: 94583
content-security-policy: script-src 'unsafe-eval' blob: 'self' meta.wikimedia.org...
content-type: text/html; charset=UTF-8
date: Thu, 10 Sep 2026 14:12:38 GMT
last-modified: Wed, 09 Sep 2026 14:12:33 GMT
nel: { "report_to": "wm_nel", "max_age": 604800, "failure_fraction": 0.05, "success_fraction": 0.0}
report-to: { "group": "wm_nel", "max_age": 604800, "endpoints": [{ "url": "https://intake-logging.wikimedia.org/v1/events?stream=w3c.reportingapi.network_error&schema_uri=/w3c/reportingapi/network_error/1.0.0" }] }
reporting-endpoints: csp-report-to-endpoint='/w/api.php?action=cspreport&format=json';
server: ATS/9.2.15
server-timing: cache;desc="hit-front", host;desc="cp3071",co_id;desc="665337278"
set-cookie: NetworkProbeLimit=0.001;Path=/;Secure;SameSite=None;Max-Age=3600
strict-transport-security: max-age=106384710; includeSubDomains; preload
vary: Accept-Encoding,X-Subdomain,Cookie,Authorization,User-Agent
x-cache: cp3071 miss, cp3071 hit/1
x-cache-status: hit-front
x-client-ip: 188.208.122.215
x-content-type-options: nosniff
x-request-id: e5da9d5b-c8b8-4a89-bc78-4dbf5906eae7
```
```
`Content-Type: text/html; charset=UTF-8` - Это очень важный заголовок. Он говорит: "ответ содержит HTML-документ, использующий кодировку UTF-8".
`Content-Encoding: gzip` - означает, что тело ответа было сжато с помощью gzip.
`Content-Length 94583` - размер тела ответа в байтах.
`Content-Language en` - Означает, что содержимое страницы на английском языке.
`Cache-Control` - определяет правила кэширования ответа. В моём случае сервер сообщает браузеру и промежуточным кэшам, как следует обращаться с этим ответом.
`Content-Security-Policy` - это политика безопасности содержимого. Она определяет, откуда странице разрешено загружать и выполнять различные ресурсы: JavaScript, изображения, стили и т. д.
```

#### Есть ли тело запроса или ответа?
- **Тело запроса:** Тут нет тела запроса. Это GET: GET /wiki/HTTP HTTP/2. Для получения страницы браузеру не требуется отправлять серверу данные в теле запроса.
- **Тело ответа:** Да. Сервер возвращает HTML-код страницы Wikipedia. Причём в моем случае: Content-Type: text/html; charset=UTF-8 и Content-Encoding: gzip. То есть сервер отправил HTML, который был предварительно сжат gzip.

#### Какие еще запросы были отправлены при загрузке страницы и почему?
При загрузке страницы было отправлено 43 HTTP-запроса. Помимо основного GET-запроса для получения HTML-документа, браузер отправил дополнительные запросы для загрузки CSS-стилей, JavaScript-файлов, изображений, SVG-иконок и других ресурсов. Эти ресурсы необходимы для правильного отображения страницы и работы интерактивных элементов сайта.
<img width="306" height="498" alt="Запросы43" src="https://github.com/user-attachments/assets/be115c81-bba2-44df-94fa-f1781a1064fb" />

### 1.2. Анализ запроса к несуществующему URL https://en.wikipedia.org/wiki/HTTPdsfdfs
При переходе по адресу https://en.wikipedia.org/wiki/HTTPdsfdfs был отправлен GET-запрос.

<img width="499" height="67" alt="красныйзапрос" src="https://github.com/user-attachments/assets/a12749db-a2b7-40ad-a51c-92c9b13cfa99" />

Код 404 Not Found означает, что сервер успешно получил и обработал запрос, но не смог найти запрошенный ресурс. В данном случае страницы HTTPdsfdfs на Wikipedia не существует, поэтому сервер вернул статус 404.

---

## Задание 2. Анализ HTTP-запросов. 
Для выполнения задания была открыта страница https://en.wikipedia.org/wiki/Special:Search на Wikipedia. В поле поиска было введено слово browser. После выполнения поиска во вкладке Network браузера был найден HTTP-запрос, связанный с выполнением поиска.

**URL запроса:**  
https://en.wikipedia.org/w/index.php?search=browser&title%3ABrowser&title=Special%3ASearch&profile=advanced&fulltext=1&advancedSearch-current=...&ns0=1

**Метод запроса:** GET.  
Метод GET используется потому, что пользователь запрашивает результаты поиска у сервера. Параметры поиска передаются непосредственно в URL запроса. Это позволяет серверу определить, какой поиск необходимо выполнить.

**Query Parameters:**  
`search=browser` — содержит поисковый запрос пользователя. Значение browser означает, что выполняется поиск по слову browser.

`title%3ABrowser` — после URL-декодирования имеет вид title:Browser и связан с условием поиска по заголовку страницы.

`title=Special%3ASearch` — указывает на специальную страницу Wikipedia Special:Search, используемую для выполнения поиска.

`profile=advanced` — указывает, что используется расширенный режим поиска.

`fulltext=1` — включает поиск по полному тексту страниц.

`advancedSearch-current` — содержит закодированную информацию о текущих настройках расширенного поиска, включая поисковое слово browser и условие поиска по заголовку Browser.

`ns0=1` — указывает на использование основного пространства имён Wikipedia (namespace 0), в котором находятся обычные статьи.

В результате сервер успешно обработал запрос и вернул статус 200 OK, что означает успешное выполнение запроса.

<img width="943" height="104" alt="незнаю" src="https://github.com/user-attachments/assets/aa521fe5-f95c-4e4b-96cc-e45662f13af9" />

---

## Задание 3. Анализ HTTP-запросов. 



















