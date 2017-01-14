Using this small library you can "cache" the output of twig code and print it later.

Use the {% defer %} tag to cache your output. Both {% defer NAME EXPRESSION %} and {% defer NAME %} EXPRESSION {% enddefer %} are possible.

Use the {% deferred %} tag to print your cached output. {% deferred NAME %} will throw an exception if nothing is cached. You can use {% deferred NAME EXPRESSION %} where EXPRESSION is your default value.

```
    {% defer foo %}
        bar
        {{ 'baz' }}
    {% enddefer %}
    {% deferred foo 'test' %}
    {# bar baz #}
    
    {% deferred foo 'test' %}
    {# test #}
    
    {% defer foo 'what' %}
    {% deferred foo %}
    {# what #}
    
    {% defer foo 'now'|date('Y-m-d') %}
    {% deferred foo %}
    {# current date im Y-m-d format #}
    
    {% deferred foo %}
    {# Will throw an exception as there is nothing defered for foo, and no default is provided #}

    {% deferred test 'foobar' %}
    {# foobar #}
```