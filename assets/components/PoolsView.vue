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
    <pagination :pagination="pagination" class="row" />
    <div class="row">
      <div class="list-group col-12">
        <router-link @click.stop :to="'/pool/' + pool.id" class="list-group-item list-group-item-action" v-for="pool in pools">
          <img :src="pool.thumbnail" :alt="pool.title" />
          {{ pool.title }}
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import Client from "../utils/Client";
import Pagination from "./Pagination";
import HydraPagination from "../utils/HydraPagination";

export default {
  name: 'PoolsView',
  components: {Pagination},
  props: {
    page: Number,
    client: Client,
  },
  data: () => ({
    pools: [],
    pagination: new HydraPagination(),
  }),
  methods: {
    update: function(page) {
      this.client.getPools(page).then(data => {
        this.pools = data['hydra:member'];
        this.pagination = new HydraPagination(data['hydra:view']);
      });
    },
  },
  created() {
    this.update(this.page);
  },
  watch: {
    page(to) {
      this.update(to);
    },
  },
}
</script>