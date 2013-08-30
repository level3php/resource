AuthenticationProcessor
====
It provides authentication based on the request. In order to implement a different authentication mechanism you just need to implement a different `Level3\Security\Authentication\AuthenticationMethod`:

```PHP
interface AuthenticationMethod
{
    public function authenticateRequest(Request $request);
}
```
It receives a Request and returns a modified version of it, including some `Level3\Security\Authentication\Credentials`. You can have a look at the default  [HMAC](https://raw.github.com/yunait/level3/master/src/Level3/Security/Authentication/Methods/HMAC.php) to get an idea of how this works.