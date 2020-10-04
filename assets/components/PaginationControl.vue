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
  <nav>
    <button class="btn" @click="navigate(first)" :disabled="!first">⮜⮜</button>
    <button class="btn" @click="navigate(previous)" :disabled="!previous">⮜</button>
    <button class="btn" @click="navigate(next)" :disabled="!next">⮞</button>
    <button class="btn" @click="navigate(last)" :disabled="!last">⮞⮞</button>
  </nav>
</template>

<script>
export default {
  name: 'PaginationControl',
  props: {
    navigate: Function,
    first: [String, Boolean, Number],
    previous: [String, Boolean, Number],
    next: [String, Boolean, Number],
    last: [String, Boolean, Number],
  },
}
</script>