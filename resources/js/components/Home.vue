<template>
  <div>
    <Header />
    <div class="container">
      <!-- <modals-container /> -->

      <!--<v-dialog />-->


      <search-component
        ref="searcComponent"
        :recording="this.recording"
        :errors="errors"
        :fetchOptions="fetchOptions"
        :optionsQueue="optionsQueue"
        :optionsAgents="optionsAgents"
        :setSelectedQueue="setSelectedQueue"
        :setSelectedCallType="setSelectedCallType"
        :getFormattedDate="getFormattedDate"
        :maxLengthInput="maxLengthInput"
        :disableQueues="disableQueues"
        :disableAgentes="disableAgentes"
      />

<!--
      <search-component :recording="recording" />
-->

      <SearchAdvancedComponent
        :recording="recording"
        :errors="errors"
        :setSelectedDurations="setSelectedDurations"
        :selectedDuration="selectedDuration"
        :maxLengthInput="maxLengthInput"
      />

      <div class="row mt-4 mb-4">
        <div class="col-12 col-md-4"></div>
        <div class="col-12 col-md-2 text-right">
          <button type="button" class="btn btn-danger btn-block" @click="clean">
            Limpiar
          </button>
        </div>
        <div class="col-12 col-md-2 text-right">
          <button type="button" class="btn btn-info btn-block" @click="search">
            Buscar
          </button>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <h5><i class="fa fa-list"></i> Resultados</h5>
          <hr />
        </div>
        <div class="col-12 col-md-4"></div>
      </div>
    </div>
    <div class="container-fluid">
      <vue-good-table
        mode="remote"
        :pagination-options="{
          enabled: true,
          nextLabel: 'siguiente',
          prevLabel: 'anterior',
          rowsPerPageLabel: 'Resultados por página',
          ofLabel: 'de',
          pageLabel: 'página',
          dropdownAllowAll: false
        }"
        :totalRows="total"
        :isLoading.sync="isLoading"
        @on-page-change="onPageChange"
        @on-sort-change="onSortChange"
        @on-per-page-change="onPerPageChange"
        @on-selected-rows-change="selectionChanged"
        :columns="columns"
        :rows="recordings"
        :rowStyleClass="rowStyleClassFn"
      >
        <template slot="table-row" slot-scope="props">
          <div v-if="props.column.field == 'audio_file'">
                  <button class="btn btn-primary btn-sm" @click.prevent="showPlaybackModal(props.row)">
                    <span class="fa fa-play-circle-o"></span>
                    PLAY
                    </button>

            <!--
            <button
              class="btn btn-primary btn-sm"
              @click.prevent="showPlaybackModal(props.row)"
            >
              PLAY
            </button>-->
          </div>
        </template>
        <div slot="emptystate">
          <div class="vgt-center-align vgt-text-disabled">
            {{ resultMessage }}
          </div>
        </div>
        <template slot="loadingContent">
          <div class="vgt-wrap">
            <div class="vgt-loading vgt-center-align">
              <span class="vgt-loading__content">Cargando...</span>
            </div>
          </div>
        </template>
      </vue-good-table>
      <div class="row mt-4 mb-4">
        <div class="col-12 col-md-4"></div>
        <div class="col-12 col-md-4 text-right">
        <!--
          <downloader-component
            :recordings="recordings"
            :isLoading="isLoading"
            :selectedRows="selectedRows"
            :getAudio="getAudio"
            :searchedFilters="searchedFilters"
            :getResultMessage="getResultMessage"
            :showErrorModal="showErrorModal"
            @update-loading="updateLoading"
            :value="resultMessage"
          />
          -->
        </div>
        <div class="col-12 col-md-2 text-right"></div>
        <div class="col-12 col-md-2 text-right"></div>
      </div>
    </div>
  </div>
</template>

<script>


//import VueGoodTablePlugin from 'vue-good-table';

import 'vue-good-table/dist/vue-good-table.css'
import { VueGoodTable } from 'vue-good-table';


import moment from "moment";
import { alphaNum, email } from "vuelidate/lib/validators";
import Vuelidate from 'vuelidate';
import AudioPlayerComponent from '../components/AudioPlayerComponent.vue'
import SearchComponent from "../components/SearchComponent.vue";
import SearchAdvancedComponent from "../components/SearchAdvancedComponent.vue";
import axios from 'axios'
import VModal from 'vue-js-modal'

Vue.use(VModal, {
        dialog: true,
        dynamic: true,
    });
Vue.use(Vuelidate);



export default {
  components: {
    AudioPlayerComponent,
    //CustomModalComponent,
    //DownloaderComponent,
    SearchComponent,
    SearchAdvancedComponent,
    VueGoodTable
    //Header
  },
  created() {
    //this.getUserMe();
    this.getQueues();
    this.getAgents();
    this.resultMessage =
      'Por favor elija los filtros búsqueda y haga click sobre el botón "BUSCAR".';
  },
  data() {
    return {
      columns: [
        {
          label: "ID de interacción",
          field: "id_interaction",
          thClass: "vgt-left-align"
        },
        {
          label: "Cuenta del agente",
          field: "agent_account"
        },
        {
          label: "Fecha",
          field: "created_at"
        },
        {
          label: "Extensión",
          field: "extension"
        },
        {
          label: "RUT",
          field: "dni"
        },
        {
          label: "Tipo de llamada",
          field: "call_type"
        },
        {
          label: "Cola",
          field: "queue"
        },
        {
          label: "ANI",
          field: "ani"
        },
        {
          label: "DNIS",
          field: "dnis"
        },
        {
          label: "Duración",
          field: "duration"
        },
        {
          label: "Disposition",
          field: "disposition"
        },
        {
          label: "Audio",
          field: "audio_file"
        }
      ],
      recording: {
        id: null,
        agent_account: null,
        extension: null,
        dni: null,
        from: null,
        to: null,
        queue: null,
        id_interaction: null,
        call_type: null,
        ani: null,
        dnis: null,
        duration: null,
        audio_file: null,
        duration_min: null,
        duration_max: null
      },
      recordings: [],
      playedAudio: null,
      clicked: null,
      errors: {
        agent_account: null,
        extension: null,
        dni: null,
        from: null,
        to: null,
        queue: null,
        id_interaction: null,
        ani: null,
        dnis: null,
        duration_min: null,
        duration_max: null
      },
      optionsQueue: [],
      optionsAgents: [],
      disableQueues: false,
      disableAgentes: false,
      selectedRows: null,
      resultMessage: null,
      total: 0,
      isLoading: false,
      searchedFilters: {},
      selectedDuration: null,
      tableSelectionMessage: null,
      maxLengthInput: 25,
      organization_id: null,
      urlReport: null
    };
  },
  provide() {
    return {
      $v: this.$v
    };
  },
  validations: {
    recording: {
      agent_account: {
        email
      },
      extension: {
        alphaNum
      },
      dni: {
        alphaNum
      },
      to: {
        greaterFromDate() {
          if (
            this.recording.from &&
            this.recording.to &&
            this.recording.to <= this.recording.from
          ) {
            return false;
          }
          return true;
        }
      },
      id_interaction: {
        alphaNum
      },
      ani: {
        alphaNum
      },
      dnis: {
        alphaNum
      }
    }
  },
  methods: {
    getFieldLabel(field) {
      if (
        ![
          "length",
          "search",
          "key",
          "order",
          "duration_min",
          "duration_max"
        ].includes(field)
      ) {
        if (field == "from") {
          return "Desde";
        } else if (field == "to") {
          return "Hasta";
        }
        return this.columns.filter(column => column.field == field)[0].label;
      }
    },
    getResultMessage(searchFilters, download) {
      if ("duration_max" in searchFilters) delete searchFilters.duration_max;
      if ("key" in searchFilters) delete searchFilters.key;
      if ("order" in searchFilters) delete searchFilters.order;
      if ("length" in searchFilters) delete searchFilters.length;

      !download
        ? (this.resultMessage = "No se han econtrando registros con: ")
        : Object.keys(searchFilters).length
        ? (this.resultMessage = " con ")
        : (this.resultMessage = "");
      let duration_column = this.columns.filter(
        column => column.field == "duration"
      )[0];
      Object.keys(searchFilters).forEach(itemKey => {
        if (itemKey.includes(duration_column.field)) {
          this.resultMessage =
            this.resultMessage +
            duration_column.label +
            " = " +
            this.selectedDuration.text;
        } else {
          this.resultMessage =
            this.resultMessage +
            this.getFieldLabel(itemKey) +
            " = " +
            searchFilters[itemKey];
        }
        if (
          Object.keys(searchFilters).indexOf(itemKey) <
          Object.keys(searchFilters).length - 1
        )
          this.resultMessage = this.resultMessage + ", ";
      });
      return this.resultMessage;
    },
    rowStyleClassFn(row) {
      return row.vgtSelected ? "custom-selected-row" : "";
    },
    selectionChanged(params) {
      this.selectedRows = params.selectedRows;
      if (this.selectedRows.length >= 1) {
        this.tableSelectionMessage =
          this.selectedRows.length == 1
            ? "fila seleccionada"
            : "filas seleccionadas";
      }
    },
    setSelectedDurations(durationsSelected) {
      this.selectedDuration = durationsSelected;
      console.log(this.selectedDuration);
      if (durationsSelected) {
        switch (durationsSelected) {
          case "1":
            this.recording.duration_min = "00:00:01";
            this.recording.duration_max = "00:00:15";
            break;
          case "2":
            this.recording.duration_min = "00:00:15";
            this.recording.duration_max = "00:00:30";
            break;
          case "3":
            this.recording.duration_min = "00:00:30";
            this.recording.duration_max = "00:00:50";
            break;
          case "4":
            this.recording.duration_min = "00:00:50";
            this.recording.duration_max = "00:01:00";
            break;
          case "5":
            this.recording.duration_min = "00:01:00";
            this.recording.duration_max = "00:05:00";
            break;
          case "6":
            this.recording.duration_min = "00:05:00";
            this.recording.duration_max = "00:07:00";
            break;
          default:
            this.recording.duration_min = "00:07:00";
            this.recording.duration_max = null;
        }
      } else {
        this.recording.duration_min = null;
        this.recording.duration_max = null;
      }
    },
    setSelectedCallType(selectedCallType) {
      this.recording.call_type = selectedCallType;
    },
    setSelectedQueue(selectedQueue) {
      this.recording.queue = selectedQueue;
    },
    getQueues(search = null) {

      console.log("iniciando get COLAS !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
      this.disableQueues = false;
      console.log(search);
      axios
        .get("api/queues")
        .then(response => {
          if (response.status === 200 && response.data) {
            this.optionsQueue = JSON.parse(atob(response.data));
            this.disableQueues = true;
          }
        }).catch(error => {
            console.log(error.response.status)
            if (error.response.status === 403 || error.response.status === 500) {
                this.$store.dispatch(AUTH_LOGOUT)
                    .then(() => {
                        this.$router.push('/login')
                    })
            }
        });
    },
    getAgents() {
      this.disableAgentes = false;
      axios
        // .get(!search ? `api/queues` : `api/queues/${btoa(escape(search))}`)
        .get("api/agents")
        .then(response => {
          if (response.status === 200 && response.data) {
            this.optionsAgents = JSON.parse(atob(response.data));
            //this.optionsAgents = JSON.parse(response.data);
            console.log("Retornando INFO");
            console.log(this.optionsAgents);
            this.disableAgentes = true;
          }
        }).catch(error => {
            console.log(error.response.status)
            if (error.response.status === 403 || error.response.status === 500) {
                this.$store.dispatch(AUTH_LOGOUT)
                    .then(() => {
                        this.$router.push('/login')
                    })
            }
        });
    },
    fetchOptions(search, loading) {
      console.log(loading);
      this.getQueues(search);
    },
    selectedRow(item) {
      this.clicked = item;
    },
    reports() {
      window.open(this.urlReport, "_blank");
    },
    getUserMe() {
        axios
          .get("/api/me")
          .then((result) => {
            this.organization_id = result.data.organization_id;
            if (this.organization_id === 2) {
              this.urlReport = "https://datastudio.google.com/s/ujJN6XIDHZo";
              
            }else if (this.organization_id === 3) {
              this.urlReport = "https://datastudio.google.com/s/mxAhXZMK7ac";
            }

          }).catch(error => {
            console.log(error.response.status)
            if (error.response.status === 403) {
              this.$store.dispatch('auth/logout');
              this.$router.push('/login');
            }
          });
      },
    clean() {
      this.resetErrors();
      (this.recording = {
        agent_account: null,
        extension: null,
        dni: null,
        from: null,
        to: null,
        queue: null,
        id_interaction: null,
        call_type: null,
        ani: null,
        dnis: null,
        duration_min: null,
        duration_max: null,
        start: 0,
        length: null,
        search: "",
        key: "id",
        order: "desc"
      }),
        (this.searchedFilters = {}),
        (this.selectedDuration = null),
        (this.resultMessage =
          'Por favor elija los filtros búsqueda y haga click sobre el botón "BUSCAR".'),
        (this.tableSelectionMessage = "");
    },
    resetErrors() {
      this.errors = {
        agent_account: null,
        extension: null,
        dni: null,
        from: null,
        to: null,
        queue: null,
        id_interaction: null,
        ani: null,
        dnis: null,
        duration_min: null,
        duration_max: null
      };
    },
    getSearchedFilters() {
      this.searchedFilters = {};
      Object.keys(this.recording).forEach(itemKey => {
        if (this.recording[itemKey]) {
          if (itemKey == "from" || itemKey == "to") {
            this.searchedFilters[itemKey] = this.getFormattedDate(
              this.recording[itemKey]
            );
          } else if (typeof this.recording[itemKey] == "string") {
            if (this.recording[itemKey].trim())
              this.searchedFilters[itemKey] = this.recording[itemKey].trim();
          } else {
            this.searchedFilters[itemKey] = this.recording[itemKey];
          }
        }
      });
    },
    search() {
      this.resetErrors();
      this.$v.$touch();
      if (this.$v.$invalid) {
        return;
      }
      if (this.recording.duration) this.getDuration(this.recording.duration);

      this.getSearchedFilters();
      axios
        .get("api/recordings", {
          params: btoa(JSON.stringify(this.searchedFilters))
        })
        .then(response => {
          if (response.status === 200 && response.data) {
            let data = JSON.parse(atob(response.data));
            this.recordings = data.recordings;
            this.total = data.total;
            if (this.recordings.length == 0)
              this.getResultMessage(this.searchedFilters);
          }
        })
        .catch(errors => {
          if (errors.response.status === 422) {
            Object.keys(errors.response.data.errors).forEach(itemKey => {
              this.getResultMessage(this.searchedFilters);
              this.errors[itemKey] =
                errors.response.data.errors[itemKey][
                  errors.response.data.errors[itemKey].length - 1
                ];
            });
          }
          if (errors.response.status === 403 || errors.response.status === 401) {
                this.$store.dispatch(AUTH_LOGOUT)
                    .then(() => {
                        this.$router.push('/login')
                    })
            }
        });
    },
    
    
  showPlaybackModal(recording) {
                this.isLoading = true;
                this.getAudio(recording.id).
                    then((audio_file) => {
                        if(audio_file){
                           
                            recording.audio_file = audio_file;
  
                            this.$modal.show(AudioPlayerComponent, {
                                text: 'custom text',
                                recording: recording,
                            }, {
                               // classes: "custom-modal-circle",
                                height: 95,
                                width: 315,
                                opacity: 0.1,
                                buttons: [{title: 'cerrar'}]
                            });
                        } else {
                            console.log("Error");
                            this.showErrorModal('"reproducir"', recording.id_interaction);
                        }
                        this.isLoading = false;
                    });
            }

    /*
    showPlaybackModal(recording) {
      console.log(recording);
      this.$router.push({
        name: "Evaluador",
        params: {
          recording_id: recording.id
        }
      });
    }*/
    ,
    showErrorModal(messageError, id_interaction, multiple = false) {
      messageError = !multiple
        ? "ha podido " + messageError + " la grabación " + id_interaction
        : "han podido " + messageError + " las grabaciones ";
      this.$modal.show("dialog", {
        text:
          '<div class="text-danger font-weight-bold">Error, no se ' +
          messageError +
          "!</div>",
        buttons: [
          {
            title: "Cerrar"
          }
        ]
      });
    },
    onPageChange(params) {
      this.recording.start = params.currentPage - 1;
      this.search();
    },
    onSortChange(params) {
      if (params[0]) {
        this.recording.key = params[0].field;
        this.recording.order = params[0].type;
        this.search();
      }
    },
    onPerPageChange(params) {
      this.recording.length = params.currentPerPage;
      this.search();
    },
    getFormattedDate(date) {
      return moment(date).format("DD/MM/YYYY");
    },
    getAudio(recording_id) {
      console.log(recording_id);
      return axios
        .get(`api/recordings/audio/${btoa(recording_id)}`)
        .then(response => {
          if (response.status === 200 && response.data) {
            return response.data;
          }
        })
        .catch(errors => {
          console.log(errors);
          return null;
        });
    },
    updateLoading() {
      this.isLoading ? (this.isLoading = false) : (this.isLoading = true);
    }
  }
};
</script>
