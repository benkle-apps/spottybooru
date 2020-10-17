import './styles/app.scss';
import Vue from 'vue';
import VueRouter from 'vue-router';
import PostsView from './components/PostsView.vue';
import PostView from "./components/PostView.vue";
import Client from "./utils/Client";
import PoolView from "./components/PoolView";
import PoolsView from "./components/PoolsView";

Vue.use(VueRouter);

const client = new Client(window.location.origin);

const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            name: 'PoolsView',
            path: '/pools/:page(\\d+)?',
            component: PoolsView,
            props: route => ({
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
                client,
            }),
        },
        {
            name: 'PoolView',
            path: '/pool/:uuid',
            component: PoolView,
            props: route => ({
                uuid: route.params.uuid,
                client,
            }),
        },
        {
            name: 'PostsView',
            path: '/:page(\\d+)?',
            component: PostsView,
            props: route => ({
                filterTags: [],
                client,
                page: parseInt(route.params.page ?? 1),
            }),
        },
        {
            name: 'FilteredPostsView',
            path: '/:tags([^/]+)/:page(\\d+)?',
            component: PostsView,
            props: route => ({
                filterTags: (route.params.tags ?? '').split(','),
                client,
                page: parseInt(route.params.page ?? 1),
            }),
        },
    ],
});

const app = new Vue({
    router,
    el: '#app',
});