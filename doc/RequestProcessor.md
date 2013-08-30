RequestProcessor
====

A `RequestProcessor` **must** implement that interface. The interface implements methods for every HTTP method. It receives a `Level3\Messages\Request` and returns a `Level3\Messages\Response` They can be chained as many times as needed. Each of these processors should perform a simple task on the request and/or the response.

```PHP
interface RequestProcessor
{
    public function find(Request $request);

    public function get(Request $request);

    public function post(Request $request);

    public function put(Request $request);

    public function delete(Request $request);
}
```
