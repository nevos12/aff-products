<template lang="html">
<ul class="mylist">
  <li v-for="(category, index) in dataCategories" class="mylist__li" :key="category.id">
    <div class="mylist__wrapper">

      <div class="mylist__plusHolder">
        <i
          :class="(isOpenObject[index]) ? 'icon-minus' :'icon-plus'"
          v-if="category.child_categories.length > 0"
          @click="toggle(index)">
        </i>
      </div>
      {{ category.name }}

      <div class="mylist__actions ml-auto mr-3">

        <button type="button" class="btn btn-sm btn-outline-warning" @click="edit(category.slug)">
          <i class="icon-pencil"></i> Edit
        </button>

        <button type="button" class="btn btn-sm btn-outline-danger" @click="destroy({slug: category.slug, name: category.name})">
          <i class="icon-trash"></i> Delete
        </button>

      </div>

    </div>

    <categories-list
      v-if="category.child_categories.length > 0 && (isOpenObject[index])"
      :data-categories="category.child_categories"
      @destroy="destroy"
      @edit="edit">
    </categories-list>

  </li>
</ul>
</template>

<script>
import Vue from 'vue'
import CategoryList from './List.vue'

export default {
  name: 'categories-list',
  props: [ 'data-categories' ],
  components: {
    CategoryList,
  },
  data() {
    return {
      isOpenObject: {},
    }
  },
  methods: {
    toggle(index) {
      _.each(Object.keys(this.isOpenObject), (val) => {
        if (index != val) {
          this.isOpenObject[val] = false
        }
      })

      Vue.set(this.isOpenObject, index, (this.isOpenObject[index]) ? false : true)
    },

    destroy({slug, name}) {
      this.$emit('destroy', {slug, name})
    },

    edit(slug) {
      this.$emit('edit', slug)
    }
  }
}
</script>
