<template>
  <div class="chat__container">
    <div class="title">
      <div class="flex my-8 gap-4 justify-center">
        <img src="~/assets/images/logo.png" class="logo" alt="Citato">
        <h1>Citato</h1>
      </div>
      <p>Context Awareness Censorship. Censor that need a censor!</p>
    </div>
    <ul class="conversation">
      <li v-for="message, index in messages" :key="'message-' + index" :class="{'chat--owned': message.role==='Sunu'}">
        <UAlert
          :title="message.role"
          :avatar="{ src: message.role==='Sunu' ? 'https://i.pravatar.cc/150?img=4' : 'https://i.pravatar.cc/150?img=2'}"
          :variant="message.role==='Sunu' ? 'soft' : 'solid' "
        >
          <template #description>
            <span v-if="message.censored" class="text-red-500">
              <i>*Kalimat ini disensor karena mengandung kata-kata kasar</i>
            </span>
            <span v-else>{{message.content}}</span>
          </template>
        </UAlert>
      </li>
    </ul>
    <div class="chat__actions">
      <UTextarea class="chat__input" v-model="input.content" autoresize @keyup.ctrl.enter="submitChat"></UTextarea> 
      <UButton icon="i-heroicons-paper-airplane" @click="submitChat" class="chat__send"></UButton>
    </div>
  </div>
</template>
<script setup>
  const route = useRoute()
  const contextAwarenes = route.query.context === 'false' ? false : true
  const messagesSample = reactive([
    [
      {
        "role": "Sunu",
        "content": "Eh, tadi aku lihat anjing gede banget di taman!",
        "censored": false
      },
      {
        "role": "Astria",
        "content": "Masa sih? Anjing apaan yang segede itu",
        "censored": false
      },
    ]
  ])

  const activeSample = ref(0)

  const input = reactive({
    "role": "Sunu",
    "content": "",
    "censored": false
  })

  const api = useApi()

  const messages = computed(() => {
    return messagesSample[activeSample.value]
  })

  const submitChat = async () => {
    await api.get('/sanctum/csrf-cookie')
    const response = await api.post('/api/v1/detect', {
      message: input.content
    }).catch((error) => {
      console.log(error)
    })

    if (response) {
      const sentenceStatus = response.data.data.choices[0].message.content
      if (sentenceStatus === 'ya' && contextAwarenes) {
        const data = JSON.parse(JSON.stringify(messagesSample[activeSample.value]))
        data.push(input)
        const response = await api.post('/api/v1/detect/cac', {
          messages: data
        }).catch((error) => {
          console.log(error)
        })

        if (response) {
          const sentenceStatus = response.data.data.choices[0].message.content
          if (sentenceStatus === 'ya') {
            //  don't show the chat
            input.censored = true
          }
          messagesSample[activeSample.value].push(JSON.parse(JSON.stringify(input)))
          input.censored = false
          input.content = ''
        }
      } else {
        if (sentenceStatus === 'ya') {
          input.censored = true
        }

        messagesSample[activeSample.value].push(JSON.parse(JSON.stringify(input)))
        input.censored = false
        input.content = ''
      }
    }
  }
</script>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Mansalva&display=swap');
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Mansalva&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

  html {
    font-family: "Inter", sans-serif;
  }

  h1 {
    font-family: "Mansalva", sans-serif;
    font-weight: 400;
    font-style: normal;
  }
  
  .chat__container {
    max-width: 640px;
    @apply w-full;
    @apply mx-auto;
    @apply py-8;
    @apply h-screen;
    @apply flex;
    @apply flex-col;
  }

  .logo {
    @apply h-16;
    @apply w-16;
    @apply block;
  }

  .title {
    @apply text-center;

    & h1 {
      @apply text-6xl;
    }

    & p {
      @apply text-lg;
    }
  }

  .conversation {
    @apply flex-1;
    @apply mt-10;
    @apply flex;
    @apply flex-col;

    & li {
      @apply my-2;
    }

    & .chat--owned {
      & > div > div {
        @apply text-right;
        @apply flex-row-reverse;
      }
    }
  }


  .chat__actions {
    @apply flex;
    @apply items-start;
    @apply gap-2;
    @apply relative;
  }

  .chat__input {
    @apply w-full;

    & textarea {
      @apply pr-10;
    }
  }

  .chat__send {
    @apply absolute;
    @apply right-2;
    @apply top-2;
  }


</style>
