import './styles/app.scss';
import Vue from 'vue';

const app = new Vue({
    el: '#app',
    delimiters: ['${', '}$'],
    data: {
        tags: [],
        posts: [],
        pagination: {
            first: false,
            previous: false,
            next: false,
            last: false
        },
    },
    methods: {
        loadTags: function() {
            fetch('api/posts/tags')
                .then(response => response.json())
                .then(data => data.map(datum => {
                    if (datum.amount > 1100) {
                        datum.amount = (datum.amount / 1000.0).toFixed(1) + 'k';
                    }
                    return datum;
                }))
                .then(data => this.tags = data);
        },
        loadImages: function(uri = '') {
            uri = '' === uri ? 'api/posts' : uri;
            fetch(uri)
                .then(response => response.json())
                .then(data => {
                    this.posts = data['hydra:member'];
                    this.pagination.first = data['hydra:view']['hydra:first'];
                    this.pagination.previous = data['hydra:view']['hydra:previous'];
                    this.pagination.next = data['hydra:view']['hydra:next'];
                    this.pagination.last = data['hydra:view']['hydra:last'];
                });
        },
        filterTag: function(tag = '') {
            this.loadImages('api/posts?tags[all][]=' + tag);
        }
    }
});

app.loadImages();
app.loadTags();