# Modularer Router mit Fluent API und dynamischen Routen

Diese README beschreibt, wie man den modularen Router einsetzt, der in PHP 8 unter Einsatz von OOP (Objektorientierte Programmierung) und Clean-Code-Prinzipien entwickelt wurde. Der Router unterstützt nun zusätzlich zu GET auch POST, PATCH und weitere HTTP-Methoden. Außerdem können dynamische Routen verwendet werden, bei denen URL-Parameter aus dem Pfadmuster extrahiert und an die Route übergeben werden.

## Überblick

Der Router unterstützt folgende Routentypen:

1. **ViewRoute**: Rendert eine View-Datei.
2. **ControllerRoute**: Ruft einen Controller auf und führt eine spezifische Action (Methode) aus.
3. **CallbackRoute**: Führt eine Callback-Funktion aus.
4. **DefaultRoute**: Wird als Fallback verwendet (z. B. für 404-Fehler, wenn keine andere Route passt).

Neben diesen Routentypen können zusätzlich auch HTTP-Methoden wie GET, POST, PATCH, PUT und DELETE verwendet werden. Dynamische Routen erlauben es, Platzhalter in der URL zu definieren (z. B. `/user/{id}`), die bei einem Request extrahiert und an die Route weitergereicht werden.

Jede Route kann um zusätzliche Daten erweitert werden und unterstützt Middleware. Die Middleware wird vor der eigentlichen Routenlogik aufgerufen und kann z. B. für Authentifizierung, Logging oder Request-Manipulation eingesetzt werden.

## Fluent API & Chaining

Der Router verfügt über eine Fluent API, die es erlaubt, Routen durch das Chaining von Methoden aufzubauen. Beispielsweise kannst du eine POST-Route mit einer Callback-Funktion wie folgt definieren:

```php
$router->post('/callback')
       ->callback(function(array $request): string {
           return "POST Callback-Route ausgeführt. Request-Daten: " . json_encode($request);
       })
       ->data(['callbackKey' => 'wert'])
       ->middleware(new SampleMiddleware());
```

### Unterstützte Methoden:

- **get(string $path)**
- **post(string $path)**
- **patch(string $path)**
- **put(string $path)**
- **delete(string $path)**

Diese Methoden beginnen jeweils die Definition einer neuen Route für die angegebene HTTP-Methode und den entsprechenden Pfad.

### Verkettbare Methoden innerhalb einer Route:

- **view(string $viewPath)**: Legt fest, dass diese Route eine View-Datei (z. B. `/views/home.php`) rendert.
- **controller(string $controller, string $action)**: Legt fest, dass diese Route einen Controller samt Methode (Action) aufruft.
- **callback(callable $callback)**: Legt fest, dass diese Route eine Callback-Funktion ausführt.
- **default()**: Definiert eine Default-Route, die beispielsweise für 404-Fehler zuständig ist.
- **data(array $data)**: Übermittelt zusätzliche Daten an die Route, die in den Request gemerged werden.
- **middleware(FunkyRouter\MiddlewareInterface $middleware)**: Fügt der Route ein Middleware-Objekt hinzu. Die Middleware wird vor der Hauptlogik ausgeführt und kann den Request verändern oder andere Aufgaben erledigen.

### Dynamische Routen

Dynamische Routen erlauben Parameter im Pfad. Du kannst Platzhalter in geschweiften Klammern definieren, zum Beispiel:
- `/user/{id}`: Der Platzhalter `{id}` wird durch den entsprechenden Wert aus dem Request ersetzt.
- `/blog/{year}/{slug}`: Mehrere dynamische Parameter werden unterstützt.

Diese Parameter werden extrahiert und in das Request-Array integriert, sodass sie in der Routenlogik (Controller, Callback etc.) zur Verfügung stehen.

## Beispiele

### 1. GET Route (ViewRoute) – Statische und Dynamische Routen

Diese Route rendert die View-Datei `views/home.php` und übergibt dabei den Seitentitel. Zusätzlich wird erläutert, wie dynamische Parameter extrahiert werden können.

```php
$router->get('/home')
       ->view(__DIR__ . '/views/home.php')
       ->data(['title' => 'Home Page'])
       ->middleware(new SampleMiddleware());

$router->get('/user/{id}')
       ->callback(function(array $request): string {
           // Der dynamische Parameter 'id' ist im Request vorhanden.
           return "User Profile for ID: " . htmlspecialchars($request['id']);
       })
       ->middleware(new SampleMiddleware());
```

### 2. POST Route (CallbackRoute)

Diese Route verarbeitet POST-Anfragen mit einer Callback-Funktion:

```php
$router->post('/form/submit')
       ->callback(function(array $request): string {
           // Verarbeite POST-Daten
           return "Formular wurde abgesendet. Daten: " . json_encode($request);
       })
       ->middleware(new SampleMiddleware());
```

### 3. PATCH Route (ControllerRoute)

Diese Route verwendet die PATCH-Methode und ruft einen Controller auf:

```php
$router->patch('/api/user/{id}')
       ->controller(HomeController::class, 'update')
       ->data(['role' => 'user'])
       ->middleware(new SampleMiddleware());
```

### 4. DefaultRoute

Die Default-Route wird verwendet, wenn keine der definierten Routen zum angefragten URI passt. Dies ist z. B. für das Anzeigen einer 404-Fehlerseite nützlich:

```php
$router->setDefaultRoute(new DefaultRoute());
```

## Dispatchen einer Anfrage

Nachdem du die Routen definiert hast, kannst du die eingehenden Anfragen dispatchen. Der Router prüft sowohl den Pfad als auch die HTTP-Methode. Dynamische Parameter werden automatisch extrahiert und dem Request-Array hinzugefügt.

```php
// Ermittlung des aktuellen HTTP-Methodentyps
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Ermittelung des Request URI, ggf. ohne Query-Parameter
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

// Optional: Zusätzliche Daten, z. B. aus $_POST oder $_GET
$requestData = array_merge($_GET, $_POST);

// Dispatch der Anfrage
$response = $router->dispatch($uri, $method, $requestData);

// Ausgabe der Antwort
echo $response;
```

## Fehlerbehandlung

Der Router und die einzelnen Routentypen werfen Exceptions, wenn Fehler auftreten, z. B.:
- Die angeforderte View-Datei existiert nicht.
- Der angegebene Controller oder die Action-Methode existiert nicht.
- Keine passende Route zu der gegebenen URL und HTTP-Methode gefunden wurde und auch keine Default-Route definiert ist.

Diese Exceptions sollten in deiner Anwendung abgefangen und entsprechend behandelt werden.

## Middleware

Middleware-Klassen müssen das `MiddlewareInterface` implementieren, das eine `handle`-Methode vorsieht. Diese Methode erhält:
- Das Request-Array.
- Einen Callback `$next`, der die nächste Middleware oder den eigentlichen Routen-Handler aufruft.

Beispiel einer Middleware-Implementierung:

```php
class SampleMiddleware implements FunkyRouter\MiddlewareInterface {
    public function handle(array $request, callable $next): mixed {
        // Vorverarbeitung, z. B. Logging oder Authentifizierung
        error_log("Middleware ausgeführt. Request: " . json_encode($request));
        $response = $next($request);
        // Nachverarbeitung (optional)
        return $response;
    }
}
```

## Zusammenfassung

Der modulare Router bietet dir eine flexible, erweiterbare und objektorientierte Lösung für das Routing in PHP-Anwendungen. Mit der Fluent API kannst du Routen für verschiedene HTTP-Methoden (GET, POST, PATCH, PUT, DELETE) erstellen, dynamische URL-Parameter verwenden und Middleware nahtlos einbinden. Passe den Router bei Bedarf an deine spezifischen Projektanforderungen an.

Weitere Anpassungen und Erweiterungen, z. B. Regular Expressions für komplexe dynamische Routen, sind möglich. Nutze diese Struktur als solide Basis für die Aufbau deiner Routing