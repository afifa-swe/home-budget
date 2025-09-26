<template>
  <v-container>
    <v-row>
      <v-col cols="12" md="6">
        <v-form @submit.prevent="onCreate">
          <v-row>
            <v-col cols="6">
              <v-text-field v-model="form.name" label="Название" />
            </v-col>
            <v-col cols="4">
              <v-select v-model="form.type" :items="['income','expense']" label="Тип" />
            </v-col>
            <v-col cols="2">
              <v-btn color="primary" type="submit">Добавить</v-btn>
            </v-col>
          </v-row>
        </v-form>
      </v-col>
    </v-row>

    <v-data-table :items="items" :headers="headers">
      <template #item.type="{ item }">
        {{ item.type }}
      </template>
      <template #item.actions="{ item }">
        <v-btn icon @click="startEdit(item)"><v-icon>mdi-pencil</v-icon></v-btn>
        <v-btn icon color="red" @click="remove(item)"><v-icon>mdi-delete</v-icon></v-btn>
      </template>
    </v-data-table>

    <v-dialog v-model="editDialog" max-width="600">
      <v-card>
        <v-card-title>Редактировать категорию</v-card-title>
        <v-card-text>
          <v-form @submit.prevent="onUpdate">
            <v-text-field v-model="form.name" label="Название" />
            <v-select v-model="form.type" :items="['income','expense']" label="Тип" />
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn color="primary" @click="onUpdate">Сохранить</v-btn>
          <v-btn text @click="editDialog=false">Отмена</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useCategoriesStore } from '../stores/useCategoriesStore'

const store = useCategoriesStore()
const items = computed(() => store.items)

const headers = [
  { title: 'Название', key: 'name' },
  { title: 'Тип', key: 'type' },
  { title: 'Действия', key: 'actions', sortable: false }
]

const form = ref({ id: null, name: '', type: 'expense' })
const editDialog = ref(false)

onMounted(async () => {
  await store.fetchCategories()
})

async function onCreate() {
  try {
    if (!form.value.name || !form.value.type) {
      alert('Введите название и выберите тип')
      return
    }
    await store.createCategory({ name: form.value.name, type: form.value.type })
    form.value.name = ''
  } catch (e) {
    alert('Ошибка создания')
  }
}

function startEdit(item: any) {
  form.value = { id: item.id, name: item.name, type: item.type }
  editDialog.value = true
}

async function onUpdate() {
  try {
    if (form.value.id) {
      if (!form.value.name || !form.value.type) {
        alert('Введите название и выберите тип')
        return
      }
      await store.updateCategory(form.value.id, { name: form.value.name, type: form.value.type })
      editDialog.value = false
    }
  } catch (e) {
    alert('Ошибка обновления')
  }
}

async function remove(item: any) {
  if (!confirm('Удалить категорию?')) return
  try {
    await store.deleteCategory(item.id)
  } catch (e) {
    alert('Ошибка удаления')
  }
}
</script>
