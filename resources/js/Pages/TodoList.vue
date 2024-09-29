<template>
  <div class="max-w-lg mx-auto my-8 p-6 bg-white rounded-lg shadow-lg">
    <h1 class="text-2xl font-bold mb-4 text-center">To-Do List</h1>
    <form @submit.prevent="addTask" class="flex mb-4">
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
    </form>
    <ul class="list-none">
      <li 
        v-for="task in tasks" 
        :key="task.id" 
        class="flex justify-between items-center bg-gray-100 p-2 mb-2 rounded-lg shadow-sm"
      >
        <span>{{ task.title }}</span>
        <button 
          @click="deleteTask(task.id)" 
          class="bg-red-500 text-white px-2 rounded hover:bg-red-600 transition duration-200"
        >
          刪除
        </button>
      </li>
    </ul>
  </div>
</template>

<script>
  import { ref } from 'vue'
  import { Inertia } from '@inertiajs/inertia'
  
  export default {
    props: {
      tasks: Array,
    },
    setup(props) {
      const newTask = ref('')
  
      const addTask = () => {
        Inertia.post('/TODOLIST/public/tasks', { title: newTask.value })
        newTask.value = ''
      }

      const deleteTask = (id) => {
        Inertia.delete(`/TODOLIST/public/tasks/${id}`)
      }
  
      return {
        newTask,
        tasks: props.tasks,
        addTask,
        deleteTask,
      }
    }
  }
</script>