import './styles/app.scss';
import Vue from 'vue';
import VueRouter from 'vue-router';
import PostsView from './components/PostsView.vue';
import PostView from "./components/PostView.vue";
import Client from "./utils/client";

Vue.use(VueRouter);

const client = new Client(window.location.origin);
const gotoPost = uuid => router.push('/post/' + uuid);

const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            name: 'PostsView',
            path: '/:page(\\d+)?',
            component: PostsView,
            props: route => ({
                filterTags: [],
                gotoPost,
                client,
                page: parseInt(route.params.page ?? 1),
            }),
        },
        {
            name: 'PostsView',
            path: '/:tags([^/]+)/:page(\\d+)?',
            component: PostsView,
            props: route => ({
                filterTags: (route.params.tags ?? '').split(','),
                gotoPost,
                client,
                page: parseInt(route.params.page ?? 1),
            }),
        },
        {
            name: 'PostView',
            path: '/post/:uuid',
            component: PostView,
            props: route => ({
                uuid: route.params.uuid,
                gotoPost,
                client,
            }),
        },
    ],
});

const app = new Vue({
    router,
    el: '#app',
});