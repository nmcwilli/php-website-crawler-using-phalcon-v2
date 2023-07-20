{% set tabs = [
    [
        'controller': 'products',
        'action': 'index',
        'title': 'Products',
        'uri': '/products/index'
    ],
    [
        'controller': 'products',
        'action': 'profile',
        'title': 'Your Profile',
        'uri': '/products/profile'
    ]
] %}

<ul class="nav nav-tabs mb-3">
    {% for controller, tab in tabs %}
        <li class="nav-item">
            <a class="nav-link {% if tab['controller'] == dispatcher.getControllerName()|lower and tab['action'] == dispatcher.getActionName() %}active{% endif %}" href="{{ tab['uri'] }}">
                {{ tab['title'] }}
            </a>
        </li>
    {% endfor %}
</ul>
