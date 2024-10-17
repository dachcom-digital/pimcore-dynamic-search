# Backend UI

It is possible to extend the backend user interface.
Listen to the event `dynamic_search.event.settings.postBuildLayout`:

```javascript
document.addEventListener(
    'dynamic_search.event.settings.postBuildLayout',
    (event) => {
        const dsSettings = event.detail.subject;

        dsSettings.panel.add({
            'xtype': 'button',
            'text': 'my button'
            // ...etc
        });
    }
);
```
