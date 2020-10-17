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
  <div v-if="pagination && (pagination.next || pagination.previous)">
    <nav class="btn-group col-12 container">
      <router-button :link="pagination.first" :disabled="!pagination.previous" class="btn-primary col-sm-2 col-md-1">
        ⮜⮜
      </router-button>
      <router-button :link="pagination.previous" class="btn-primary col-sm-2 col-md-1">
        ⮜
      </router-button>
      <span class="btn btn-light col-sm-6 col-md-8">{{ pagination.page }} of {{ pagination.count }}</span>
      <router-button :link="pagination.next" class="btn-primary col-sm-2 col-md-1">
        ⮞
      </router-button>
      <router-button :link="pagination.last" :disabled="!pagination.next" class="btn-primary col-sm-2 col-md-1">
        ⮞⮞
      </router-button>
    </nav>
  </div>
</template>

<script>
import RouterButton from "./RouterButton";
import HydraPagination from "../utils/HydraPagination";

export default {
  name: 'Pagination',
  components: {RouterButton,},
  props: {
    pagination: [Boolean, HydraPagination],
  },

}
</script>