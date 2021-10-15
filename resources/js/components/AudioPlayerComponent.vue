<template>
  <div>


<audio controls v-if="file" id ="audio-player" class="audioPlayer">
            <source v-bind:src="file">
            Your browser does not support the audio element.
  </audio>

  <!--        
<audio controls>
  <source src="https://file-examples-com.github.io/uploads/2017/11/file_example_MP3_5MG.mp3" type="audio/mpeg">
  Your browser does not support the audio tag.
</audio> -->

<!--
 <audio  id="myAudio" controls  v-if="file" class="audioPlayer">
            <source v-bind:src="file">
            Your browser does not support the audio element.
            </audio>
            -->
 </div>
</template>



<script>
  // import moment from 'moment';
  import axios from 'axios';

  export default {
    created() {
     

      console.log("MOUNTED"+this.recording.id);
      this.getAudio(this.recording.id);

      

    },
    props: [
      'text',
      'recording'
    ],
    data() {
      return {

        file: null,
        id : null
      
      }
    },
    methods: {
      getAudio(recording_id) {
        console.log(recording_id);
        return axios.get(`api/recordings/audio/${btoa(recording_id)}`)
          .then(response => {
            if (response.status === 200 && response.data) {
              console.log(response.data);
              this.recordingData = response.data;
              this.file = response.data.audio_file;
              // con el file general URL
              this.getAudioFtpURL( response.data.audio_file , response.data.id_interaction);
              
              this.optionsAgent = [{
                value: response.data.agent_account,
                text: response.data.agent_account
              }];
              this.selectedAgent = response.data.agent_account;
              // return response.data;
            }
          }).catch(errors => {
            console.log(errors);
            return null;
          });
      }, //Crear otro metodo que permita transformar el FTP a url File
   getAudioFtpURL(file,id){
    
    

     //AXios llamar a web service AQUI VA EL ENDPOINT DEFINITVO PARA EXTRAER EL FTP
     return axios.post("api/s3toURI",{

        "linkRecording":file,
        "idCall":id,
        "urlToReturn":"https://record.uss.cl/storage/"
        //"urlToReturn":"http://127.0.0.1:8000/storage/"

     }).then(response => {

      //console.log(response.data.url);

      var audio = document.getElementById("audio-player");
      audio.src = response.data.url;
      audio.play();
      this.$file = response.data.url;


     });

   }
    
    },
    computed: {

            

    }
  }
</script>



<style scoped>
  * {
    box-sizing: initial !important;
  }

  *,
  *::before,
  *::after {
    box-sizing: none !important;
  }

  html,
  body {
    width: 100%;
    height: 100%
  }

  body {
    background: linear-gradient(to bottom, #238999, #313C60)
  }



</style>