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
      <tags-list class="col-xxl-1 col-md-2 col-sm-12" v-bind:tags="tags" v-bind:navigate="addTagToFilter" v-bind:showControls="true"></tags-list>
      <posts-grid class="col-xxl-11 col-md-10 col-sm-12" v-bind:posts="posts" v-bind:navigate="gotoPost"></posts-grid>
      <pagination-control
          v-bind:navigate="changePage"
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
import Client from "../utils/client";

const extractPageFromUri = uri => {
  if (!uri) {
    return false;
  }
  uri = new URL(uri, 'http://somewhere.org/');
  return uri.searchParams.get('page') ?? false;
};

export default {
  name: 'PostsView',
  components: {PaginationControl, PostsGrid, TagsList},
  props: {
    filterTags: [Array, String],
    changeTags: Function,
    gotoPost: Function,
    client: Client,
    page: Number,
  },
  data: () => ({
    tags: [],
    posts: [],
    pagination: {
      first: 1,
      previous: false,
      next: false,
      last: false,
    },
  }),
  methods: {
    update: function(filterTags, page) {
      this.client.getTags(filterTags).then(data => this.tags = data);
      this.client.getPosts(filterTags, page).then(data => {
        this.posts = data['hydra:member'];
        this.pagination.first = extractPageFromUri(data['hydra:view']['hydra:first']);
        this.pagination.previous = extractPageFromUri(data['hydra:view']['hydra:previous']);
        this.pagination.next = extractPageFromUri(data['hydra:view']['hydra:next']);
        this.pagination.last = extractPageFromUri(data['hydra:view']['hydra:last']);
      });
    },
    addTagToFilter: function(tag, operation) {
      operation = operation || 'set';
      let tags = this.filterTags.filter(v => !(v in [tag, '-' + tag]));
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
      this.changeTags(tags.sort());
    },
    changePage: function(page) {
      this.changeTags(this.filterTags.sort(), page);
    }
  },
  created() {
    this.update(this.filterTags, this.page);
  },
  watch: {
    filterTags(to) {
      this.update(to, 1);
    },
    page(to) {
      this.update(this.filterTags, to);
    }
  },
}
</script>