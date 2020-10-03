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
    <div class="row tag" v-for="tag in tags">
      <a class="col-10" @click="navigate(tag.title)" href="#" :title="tag.title">{{ tag.title }}</a>
      <span class="col-1 btn btn-success hover" @click="navigate(tag.title, 'add')">+</span>
      <span class="col-2 badge badge-info no-hover">
        <span>{{ tag.amount }}</span>
      </span>
      <span class="col-1 btn btn-danger hover" @click="navigate(tag.title, 'remove')">-</span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TagsList',
  props: {
    tags: Array,
    navigate: Function,
  },
};
</script>