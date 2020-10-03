<!--
  - Copyright 2020 Benjamin Kleiner
  -
  - Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
  - documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
  - rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
  - permit persons to whom the Software is furnished to do so, subject to the following conditions:
  -
  - The above copyright notice and this permission notice shall be included in all copies
  - or substantial portions of the Software.
  -
  - THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO
  - THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
  - IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
  - WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR
  - THE USE OR OTHER DEALINGS IN THE SOFTWARE.
  -->

<template>
  <main class="container-fluid">
    <div class="row">
      <tags-list class="col-xxl-1 col-md-2 col-sm-12" v-bind:tags="tagsData" v-bind:navigate="addTagToFilter"></tags-list>
      <posts-grid class="col-xxl-11 col-md-10 col-sm-12" v-bind:posts="posts"></posts-grid>
      <pagination-control
          v-bind:navigate="loadImages"
          v-bind:first="pagination.first"
          v-bind:previous="pagination.previous"
          v-bind:next="pagination.next"
          v-bind:last="pagination.last"
      ></pagination-control>
    </div>
  </main>
</template>

<script>
import PostsGrid from "./PostsGrid.vue";
import TagsList from "./TagsList.vue";
import PaginationControl from "./PaginationControl.vue";

export default {
  name: 'PostsView',
  components: {PaginationControl, PostsGrid, TagsList},
  props: {
    tags: [Array, String],
    changeTags: Function,
  },
  data: () => ({
    tagsData: [],
    posts: [],
    pagination: {
      first: false,
      previous: false,
      next: false,
      last: false,
    },
  }),
  created: function() {
    this.loadImages();
    this.loadTags();
  },
  methods: {
    addTagsFilter: function(uri) {
      const tags = this.tags;
      tags.filter(p => !!p).forEach(tag => {
        switch (tag[0]) {
          case '-':
            uri.searchParams.append('tags[none][]', tag.substr(1));
            break;
          case '+':
            uri.searchParams.append('tags[all][]', tag.substr(1));
            break;
          default:
            uri.searchParams.append('tags[all][]', tag.toString());
            break;
        }
      });
    },
    loadTags: function() {
      const uri = new URL('api/posts/tags', location.href);
      this.addTagsFilter(uri);
      fetch(uri.toString())
          .then(response => response.json())
          .then(data => data.map(datum => {
            if (datum.amount > 1100) {
              datum.amount = (datum.amount / 1000.0).toFixed(1) + 'k';
            }
            return datum;
          }))
          .then(data => this.tagsData = data);
    },
    loadImages: function() {
      const uri = new URL('api/posts', location.href);
      this.addTagsFilter(uri);
      fetch(uri.toString())
          .then(response => response.json())
          .then(data => {
            this.posts = data['hydra:member'];
            this.pagination.first = data['hydra:view']['hydra:first'];
            this.pagination.previous = data['hydra:view']['hydra:previous'];
            this.pagination.next = data['hydra:view']['hydra:next'];
            this.pagination.last = data['hydra:view']['hydra:last'];
          });
    },
    addTagToFilter: function(tag, operation) {
      operation = operation || 'set';
      let tags = this.tags;
      switch (operation) {
        case 'set':
          tags = [tag];
          break;
        case 'add':
          tags.push(tag);
          break;
        case 'remove':
          tags.push('-' + tag);
          break;
      }
      this.changeTags(tags);
    },
  },
  watch: {
    $route(to, from) {
      this.loadImages();
      this.loadTags();
    },
  },
}
</script>