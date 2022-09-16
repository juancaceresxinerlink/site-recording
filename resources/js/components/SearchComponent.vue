<template>
    <div class="row">
        <div class="col-12">
            <h5><i class="fa fa-search"></i> Búsqueda</h5>
            <hr />
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group row">
                <label for="agente" class="col-sm-4 col-form-label text-right"
                    >Cuenta de agente:</label
                >
                <div class="col-sm-8">
                    <!-- <input
            type="text"
            class="form-control"
            v-model="recording.agent_account"
            :maxlength="40"
          />
          <small v-if="errors" class="text-danger font-weight-bold">{{
            errors.agent_account
          }}</small>
          <small
            v-if="!$v.recording.agent_account.email"
            class="text-danger font-weight-bold"
          >
            El campo "Cuenta de agente" debe ser una dirección de correo válida.
          </small> -->
                    <b-form-select
                        v-if="disableAgentes"
                        v-model="recording.agent_account"
                        :options="optionsAgents"
                        class="style-chooser"
                    ></b-form-select>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group row">
                <label
                    for="extension"
                    class="col-sm-4 col-form-label text-right"
                    >Extensión:</label
                >
                <div class="col-sm-8">
                    <input
                        type="text"
                        class="form-control"
                        id="extension"
                        v-model="recording.extension"
                        :maxlength="maxLengthInput"
                    />
                    <small v-if="errors" class="text-danger font-weight-bold">{{
                        errors.extension
                    }}</small>
                    <small
                        v-if="!$v.recording.extension.alphaNum"
                        class="text-danger font-weight-bold"
                    >
                        El campo "Extensión" solo puede contener letras y
                        números.
                    </small>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group row">
                <label class="col-sm-4 col-form-label text-right">RUT:</label>
                <div class="col-sm-8">
                    <input
                        type="text"
                        class="form-control"
                        v-model="recording.dni"
                        :maxlength="maxLengthInput"
                    />
                    <small v-if="errors" class="text-danger font-weight-bold">{{
                        errors.dni
                    }}</small>
                    <small
                        v-if="!$v.recording.dni.alphaNum"
                        class="text-danger font-weight-bold"
                        >El campo "DNI" solo puede contener letras y
                        números.</small
                    >
                    >
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="form-group row">
                <label class="col-sm-4 col-form-label text-right">Desde:</label>
                <div class="col-sm-8">
                    <date-picker
                        :language="es"
                        :format="dateFormat"
                        input-class="form-control"
                        v-model="recording.from"
                    ></date-picker>
                    <small v-if="errors" class="text-danger font-weight-bold">{{
                        errors.from
                    }}</small>
                    <small
                        v-if="recording.to && !$v.recording.to.greaterFromDate"
                        class="text-danger font-weight-bold"
                    >
                        El campo "Desde" debe ser una fecha anterior a
                        {{ getFormattedDate(this.recording.to) }}.
                    </small>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group row">
                <label class="col-sm-4 col-form-label text-right">Hasta:</label>
                <div class="col-sm-8">
                    <date-picker
                        ref="to"
                        :language="es"
                        :format="dateFormat"
                        input-class="form-control"
                        v-model="recording.to"
                    ></date-picker>
                    <small v-if="errors" class="text-danger font-weight-bold">{{
                        errors.to
                    }}</small>
                    <small
                        v-if="!$v.recording.to.greaterFromDate"
                        class="text-danger font-weight-bold"
                    >
                        El campo "Hasta" debe ser una fecha posterior a
                        {{ getFormattedDate(this.recording.from) }}.
                    </small>
                </div>
            </div>
        </div>

        <!--Modificar por un multiple select-->
        <div class="col-12 col-md-4">
            <div class="form-group row">
                <label class="col-sm-4 col-form-label text-right">Cola:</label>
                <div class="col-sm-8">
                    <multiselect
                        v-model="recording.queue"
                        :options="optionsQueue"
                        multiple
                    ></multiselect>

                    <!--
          <b-form-select
            v-if="disableQueues"
            v-model="recording.queue"
            :options="optionsQueue"
            class="style-chooser"
          ></b-form-select>
        -->
                </div>
            </div>
        </div>

        <!-- <div class="col-12 col-md-4"></div> -->
        <div class="col-12 col-md-4">
            <div class="form-group row">
                <label class="col-sm-4 col-form-label text-right"
                    >ID de interacción:</label
                >
                <div class="col-sm-8">
                    <input
                        type="text"
                        class="form-control"
                        v-model="recording.id_interaction"
                        :maxlength="maxLengthInput"
                    />
                    <small v-if="errors" class="text-danger font-weight-bold">{{
                        errors.id_interaction
                    }}</small>
                    <small
                        v-if="!$v.recording.id_interaction.alphaNum"
                        class="text-danger font-weight-bold"
                    >
                        El campo "ID de interacción" solo puede contener letras
                        y números.
                    </small>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group row">
                <label class="col-sm-4 col-form-label text-right">ID </label>
                <div class="col-sm-8">
                    <input
                        type="text"
                        class="form-control"
                        v-model="recording.id"
                        :maxlength="maxLengthInput"
                    />
                    <small v-if="errors" class="text-danger font-weight-bold">{{
                        errors.id
                    }}</small>
                    <small
                        v-if="!$v.recording.id.numeric"
                        class="text-danger font-weight-bold"
                    >
                        El campo "ID de interacción" solo puede contener
                        números.
                    </small>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group row">
                <label class="col-sm-4 col-form-label text-right"
                    >Tipo de llamada:</label
                >
                <div class="col-sm-8">
                    <!-- <v-select class="style-chooser" :options="callTypes" :searchable="false" :value="recording.call_type" @input="setSelectedCallType"/> -->
                    <b-form-select
                        v-model="recording.call_type"
                        :options="callTypes"
                        class="style-chooser"
                    ></b-form-select>
                </div>
            </div>
        </div>
    </div>
</template>
<style>
.style-chooser .vs__search {
    height: calc(1.6em + 0.125rem + 2px);
}
.style-chooser .vs__dropdown-toggle {
    border-radius: 0px;
}
</style>

<script>
import Datepicker from "vuejs-datepicker";
Vue.component("date-picker", Datepicker);
import { es } from "vuejs-datepicker/dist/locale";

import { BFormSelect } from "bootstrap-vue";
Vue.component("b-form-select", BFormSelect);

import Multiselect from "vue-multiselect";

Vue.component("multiselect", Multiselect);

export default {
    inject: ["$v"],
    props: [
        "recording",
        "errors",
        "fetchOptions",
        "optionsQueue",
        "optionsAgents",
        "selectedQueue",
        "setSelectedQueue",
        "setSelectedCallType",
        "getFormattedDate",
        "maxLengthInput",
        "disableQueues",
        "disableAgentes",
    ],
    data() {
        return {
            callTypes: ["Inbound", "Outbound"],
            es: es,
            dateFormat: "dd/MM/yyyy",
        };
    },
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
