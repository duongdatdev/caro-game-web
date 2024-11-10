<template>
  <div>
    <div class="connection-status" :class="{ connected: isConnected }">
      Status: {{ connectionStatus }}
    </div>
    <button @click="testConnection">Test Connection</button>
    <input v-model="message" @keyup.enter="sendMessage" placeholder="Type a message" />
    <button @click="sendMessage">Send</button>
    <ul>
      <li v-for="msg in messages" :key="msg">{{ msg }}</li>
    </ul>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted, onUnmounted } from '@vue/runtime-core';
import axios from 'axios';

export default defineComponent({
  name: 'TestMessage',
  setup() {
    const message = ref('');
    const messages = ref<string[]>([]);
    const isConnected = ref(false);
    const connectionStatus = ref('Disconnected');

    const testConnection = async () => {
      try {
        await axios.post('/broadcasting/auth', {
          channel_name: 'test-channel',
          socket_id: window.Echo.socketId()
        });
        messages.value.push('Connection test successful');
      } catch (error) {
        console.error('Connection test failed:', error);
        messages.value.push('Connection test failed');
      }
    };

    const sendMessage = async () => {
      if (message.value.trim() === '') return;
      try {
        await axios.post('/send-test-message', { message: message.value });
        message.value = '';
      } catch (error) {
        console.error('Error sending message:', error);
      }
    };

    onMounted(() => {
      if (window.Echo) {
        window.Echo.connector.pusher.connection.bind('connected', () => {
          console.log('Pusher connected successfully in TestMessage component');
          isConnected.value = true;
          connectionStatus.value = 'Connected';
        });

        window.Echo.connector.pusher.connection.bind('disconnected', () => {
          console.log('Pusher disconnected');
          isConnected.value = false;
          connectionStatus.value = 'Disconnected';
        });

        window.Echo.connector.pusher.connection.bind('error', (error: any) => {
          console.error('Pusher connection error:', error);
          connectionStatus.value = 'Error: ' + error.message;
        });

        window.Echo.channel('test-channel')
          .listen('.test-event', (e: any) => {
            messages.value.push(e);
          });
      }
    });

    onUnmounted(() => {
      if (window.Echo) {
        window.Echo.leave('test-channel');
        window.Echo.disconnect();
      }
    });

    return {
      message,
      messages,
      sendMessage,
      testConnection,
      isConnected,
      connectionStatus
    };
  }
});
</script>

<style scoped>
.connection-status {
  padding: 8px;
  margin-bottom: 16px;
  background-color: #ff4444;
  color: white;
  border-radius: 4px;
}

.connection-status.connected {
  background-color: #4CAF50;
}
</style>