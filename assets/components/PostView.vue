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
      <tags-list class="col-xxl-1 col-md-2 col-sm-12" v-bind:tags="tags" v-bind:navigate="gotoTag"
                 v-bind:showControls="false"></tags-list>
      <figure class="col-xxl-11 col-md-10 col-sm-12">
        <img :src="post.file" :alt="post.title"/>
      </figure>
    </div>
  </main>
</template>

<script>
import TagsList from "./TagsList.vue";
import Client from "../utils/client";

export default {
  name: 'PostView',
  components: {TagsList},
  props: {
    uuid: String,
    changeTags: Function,
    client: Client,
  },
  data: () => ({
    tags: [],
    post: {
      file: '',
      title: '',
    },
  }),
  methods: {
    update: function (uuid) {
      this.client.getTagsForPost(uuid).then(data => this.tags = data);
      this.client.getPost(uuid).then(data => this.post = data);
    },
    gotoTag: function (tag) {
      this.changeTags([tag]);
    },
  },
  created() {
    this.update(this.uuid);
  },
  watch: {
    uuid(to) {
      this.update(to);
    },
  },
}
</script>