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
  <div class="container">
    <div class="row">
      <div class="col-xl-2 col-lg-3 col-md-4 col-6 text-center" :data-safety="post.safety" v-for="post in posts">
        <router-link :to="'/post/' + post.id" @click.stop.prevent="navigate(post.id)" class="d-block mb-4 h-100">
          <img class="img-fluid img-thumbnail" :src="post.thumbnail" :alt="post.title" :title="post.title">
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PostsGrid',
  props: {
    posts: Array,
    navigate: Function,
  },
};
</script>