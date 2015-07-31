import App from 'flarum/App';
import store from 'flarum/initializers/store';
import preload from 'flarum/initializers/preload';
import routes from 'flarum/initializers/routes';
import boot from 'flarum/initializers/boot';

const app = new App();

app.initializers.add('store', store);
app.initializers.add('routes', routes);

app.initializers.add('preload', preload, -100);
app.initializers.add('boot', boot, -100);

export default app;
