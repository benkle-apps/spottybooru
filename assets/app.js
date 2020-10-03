import './styles/app.scss';
import Vue from 'vue';
import VueRouter from 'vue-router';
import PostsView from './components/PostsView.vue';

Vue.use(VueRouter);

const changeTags = (tags) => router.push('/' + tags.join(','));

const router = new VueRouter({
    mode: 'history',
    routes: [
        {path: '/', component: PostsView, props: {tags: [], changeTags},},
        {
            path: '/:tags',
            component: PostsView,
            props: route => ({
                tags: route.params.tags.split(',') || [],
                changeTags,
            }),
        },
    ],
});

const app = new Vue({
    router,
    el: '#app',
});