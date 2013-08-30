AuthorizationProcessor
===
Everything is already implemented in `Level3\Security\Authorization\AbstractAuthorizationProcessor`. If you want to implement a custom authorization model, just implement the abstract method in a subclass and *chain* your class in the `RequestProcessor` chain:

```PHP
protected function hasAccess(Request $request, $methodName)
```
This method returns `true` if the request should be allowed and `false` if not