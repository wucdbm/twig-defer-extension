Using this small library you can "cache" the output of twig code and print it later.

Use the {% defer %} tag to cache your output. Both {% defer NAME EXPRESSION %} and {% defer NAME %} EXPRESSION {% enddefer %} are possible.

```
    {% defer 'someKey' 'defaultValueExpression' %}
    {% defer someKeyVariable 'someDefaultValueExpression' %}
    {% defer 'someKey' %}
        someBlock {{ with Variables }}
    {% enddefer %}
    
    {% if 'someKey' is deferred %}
    {% if someVariableValue is deferred %}
    
    {{ someKey|deferred('defaultValue') }}
```