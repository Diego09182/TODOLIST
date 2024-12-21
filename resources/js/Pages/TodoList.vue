<template>
  <div class="max-w-lg mx-auto my-8 p-6 bg-white rounded-lg shadow-lg">
    <h1 class="text-2xl font-bold mb-4 text-center">任務事項清單</h1>
    <form @submit.prevent="addTask" class="flex flex-col mb-4">
      <div class="flex mb-2">
        <input 
          v-model="newTask"
          type="text" 
          placeholder="添加各種任務" 
          class="flex-grow p-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        <button 
          type="submit"
          class="bg-blue-500 text-white px-4 rounded-r-lg hover:bg-blue-600 transition duration-200"
        >
          添加任務
        </button>
      </div>
      <input 
        ref="fileInput" 
        type="file" 
        @change="handleFileUpload" 
        class="mb-2 p-2 border border-gray-300 rounded focus:outline-none"
      />
    </form>
    <ul class="list-none">
      <li 
        v-for="task in tasks.data" 
        :key="task.id" 
        class="flex flex-col bg-gray-100 p-2 mb-4 rounded-lg shadow-sm"
      >
        <div class="flex justify-between items-center">
          <div v-if="editingTask === task.id">
            <input 
              v-model="editTaskTitle" 
              type="text" 
              class="flex-grow p-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <button 
              @click="updateTask(task.id)" 
              class="bg-green-500 text-white px-4 rounded-r-lg hover:bg-green-600 transition duration-200"
            >
              保存
            </button>
            <button 
              @click="cancelEdit"
              class="bg-gray-500 text-white px-4 rounded-r-lg hover:bg-gray-600 transition duration-200 ml-2"
            >
              取消
            </button>
          </div>
          <div v-else>
            <span class="font-semibold">{{ task.title }}</span>
            <button 
              @click="editTask(task)" 
              class="bg-yellow-500 text-white px-2 rounded hover:bg-yellow-600 transition duration-200 ml-2"
            >
              編輯
            </button>
            <button 
              @click="deleteTask(task.id)" 
              class="bg-red-500 text-white px-2 rounded hover:bg-red-600 transition duration-200 ml-2"
            >
              刪除任務
            </button>
          </div>
        </div>

        <div v-if="task.file_path" class="mt-2">
          <a :href="`/TODOLIST/public/storage/${task.file_path}`" target="_blank" class="text-blue-500 hover:underline">
            查看上傳檔案
          </a>
        </div>

        <form @submit.prevent="addList(task.id)" class="flex mt-2">
          <input 
            v-model="newList[task.id]" 
            type="text" 
            placeholder="添加列表" 
            class="flex-grow p-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
          <button 
            type="submit" 
            class="bg-green-500 text-white px-4 rounded-r-lg hover:bg-green-600 transition duration-200"
          >
            添加列表
          </button>
        </form>

        <ul class="list-disc ml-4 mt-2">
          <li 
            v-for="list in task.lists" 
            :key="list.id" 
            class="flex justify-between items-center"
          >
            <div v-if="editingList === list.id">
              <input 
                v-model="editListTitle[list.id]" 
                type="text" 
                class="flex-grow p-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
              <button 
                @click="updateList(task.id, list.id)" 
                class="bg-green-500 text-white px-2 rounded hover:bg-green-600 transition duration-200 ml-2"
              >
                保存
              </button>
              <button 
                @click="cancelListEdit" 
                class="bg-gray-500 text-white px-2 rounded hover:bg-gray-600 transition duration-200 ml-2"
              >
                取消
              </button>
            </div>
            <div v-else>
              <span>{{ list.name }}</span>
              <button
                @click="editList(list)" 
                class="bg-yellow-500 text-white px-2 rounded hover:bg-yellow-600 transition duration-200 ml-2"
              >
                編輯
              </button>
              <button 
                @click="deleteList(task.id, list.id)" 
                class="bg-red-500 text-white px-2 rounded hover:bg-red-600 transition duration-200 ml-2"
              >
                刪除列表
              </button>
            </div>
          </li>
        </ul>
        
      </li>
    </ul>
    <div class="flex justify-center mt-4">
      <button 
        v-for="link in tasks.links" 
        :key="link.label" 
        :disabled="!link.url" 
        @click="getPage(link.url)" 
        class="mx-1 px-3 py-1 border rounded hover:bg-blue-500 hover:text-white"
        :class="{ 'bg-blue-500 text-white': link.active, 'bg-gray-200': !link.url }"
      >
        <span v-if="link.label.includes('Previous')">««</span>
        <span v-else-if="link.label.includes('Next')">»»</span>
        <span v-else>{{ link.label }}</span>
      </button>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'

export default {
  props: {
    tasks: Object,
  },
  setup(props) {
    const newTask = ref('')
    const newList = ref({})
    const file = ref(null)

    const editingTask = ref(null)
    const editTaskTitle = ref('')
    
    const editingList = ref(null)
    const editListTitle = ref({})

    const handleFileUpload = (e) => {
      file.value = e.target.files[0]
    }

    const addTask = () => {
      const formData = new FormData()
      formData.append('title', newTask.value)
      if (file.value) {
        formData.append('file', file.value)
      }
      Inertia.post('/TODOLIST/public/tasks', formData)
      newTask.value = ''
      file.value = null
      document.querySelector("input[type='file']").value = ""
    }

    const deleteTask = (id) => {
      Inertia.delete(`/TODOLIST/public/tasks/${id}`)
    }

    const editTask = (task) => {
      editingTask.value = task.id
      editTaskTitle.value = task.title
    }

    const updateTask = (taskId) => {
      const formData = new FormData()
      formData.append('title', editTaskTitle.value)
      if (file.value) {
        formData.append('file', file.value)
      }
      Inertia.post(`/TODOLIST/public/tasks/${taskId}`, formData, { method: 'put' })
      cancelEdit()
    }

    const cancelEdit = () => {
      editingTask.value = null
      editTaskTitle.value = ''
    }

    const addList = (taskId) => {
      const formData = new FormData()
      formData.append('name', newList.value[taskId])
      Inertia.post(`/TODOLIST/public/tasks/${taskId}/lists`, formData)
      newList.value[taskId] = ''
    }

    const editList = (list) => {
      editingList.value = list.id
      editListTitle.value[list.id] = list.name
    }

    const updateList = (taskId, listId) => {
      const formData = new FormData()
      formData.append('name', editListTitle.value[listId])
      Inertia.post(`/TODOLIST/public/tasks/${taskId}/lists/${listId}`, formData, { method: 'put' })
      cancelListEdit()
    }

    const deleteList = (taskId, listId) => {
      Inertia.delete(`/TODOLIST/public/tasks/${taskId}/lists/${listId}`)
    }

    const cancelListEdit = () => {
      editingList.value = null
      editListTitle.value = {}
    }

    const getPage = (url) => {
      Inertia.get(url)
    }

    return {
      newTask,
      newList,
      file,
      editingTask,
      editTaskTitle,
      editingList,
      editListTitle,
      handleFileUpload,
      addTask,
      deleteTask,
      editTask,
      updateTask,
      cancelEdit,
      addList,
      editList,
      updateList,
      deleteList,
      cancelListEdit,
      getPage,
    }
  }
}
</script>