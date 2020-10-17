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
      <tags-list class="col-xxl-1 col-md-2 col-sm-12" :tags="tags" :navigate="gotoTag" :showControls="false"></tags-list>
      <div class="col-xxl-11 col-md-10 col-sm-12 post container">
        <div class="row" v-if="post.pools.length">
          <nav class="btn-group col-12 container" v-for="pool in post.pools">
            <router-button :link="pool.previous" class="btn-primary col-sm-2 col-md-1">
              ⮜
            </router-button>
            <router-button :link="'/pool/' + pool.id" class="btn btn-light col-sm-8 col-md-10">
              {{ pool.title }}
            </router-button>
            <router-button :link="pool.next" class="btn-primary col-sm-2 col-md-1">
              ⮞
            </router-button>
          </nav>
        </div>
        <div class="row">
          <div class="col-12">
            <img :src="post.file" :alt="post.title" v-if="post.mime.startsWith('image/')"/>
            <video controls preload="metadata" :poster="post.thumbnail" v-if="post.mime.startsWith('video/')">
              <source :src="post.file" :type="post.mime"/>
            </video>
            <object :width="post.width" :height="post.height" type="application/x-shockwave-flash"
                    v-if="'application/x-shockwave-flash' === post.mime">
              <param name="movie" :value="post.file"/>
              <param name="wmode" value="opaque"/>
              <embed :src="post.file" allowscriptaccess="never" :width="post.width" :height="post.height"/>
            </object>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <h3>{{ post.title }}</h3>
            <div v-html="description"></div>
            <hr/>
            <a :href="post.file">Download</a>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script>
import TagsList from "./TagsList";
import Client from "../utils/client";
import RouterButton from "./RouterButton";

export default {
  name: 'PostView',
  components: {TagsList, RouterButton,},
  props: {
    uuid: String,
    client: Client,
    gotoPost: Function,
  },
  data: () => ({
    tags: [],
    post: {
      file: '',
      title: '',
      description: '',
      mime: '',
      thumbnail: '',
      width: 0,
      height: 0,
      pools: [],
    },
  }),
  computed: {
    description: function () {
      return (this.post.description ?? '').replaceAll(/[\n\r]+/ig, '<br/>');
    }
  },
  methods: {
    update: function (uuid) {
      this.client.getTagsForPost(uuid).then(data => this.tags = data);
      this.client.getPost(uuid).then(data => this.post = data);
    },
    gotoTag: function (tag) {
      return '/' + tag;
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