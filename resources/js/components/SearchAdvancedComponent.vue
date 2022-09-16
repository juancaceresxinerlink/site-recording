<template>
    <div class="row mt-4">
        <div class="col-12">
            <h5><i class="fa fa-search"></i> Búsqueda Avanzada</h5>
            <hr />
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group row">
                <label class="col-sm-4 col-form-label text-right">ANI:</label>
                <div class="col-sm-8">
                    <input
                        type="text"
                        class="form-control"
                        v-model="recording.ani"
                        :maxlength="maxLengthInput"
                    />
                    <small v-if="errors" class="text-danger font-weight-bold">{{
                        errors.ani
                    }}</small>
                    <small
                        v-if="!$v.recording.ani.alphaNum"
                        class="text-danger font-weight-bold"
                    >
                        El campo "ANI" solo puede contener letras y números.
                    </small>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group row">
                <label class="col-sm-4 col-form-label text-right">DNIS:</label>
                <div class="col-sm-8">
                    <input
                        type="text"
                        class="form-control"
                        v-model="recording.dnis"
                        :maxlength="maxLengthInput"
                    />
                    <small v-if="errors" class="text-danger font-weight-bold">{{
                        errors.dnis
                    }}</small>
                    <small
                        v-if="!$v.recording.dnis.alphaNum"
                        class="text-danger font-weight-bold"
                        >El campo "DNIS" solo puede contener letras y
                        números.</small
                    >
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group row">
                <label class="col-sm-4 col-form-label text-right"
                    >Duración:</label
                >
                <div class="col-sm-8">
                    <b-form-select
                        :options="durationOptions"
                        @change="setSelectedDurations"
                        class="style-chooser"
                    ></b-form-select>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    inject: ["$v"],
    props: [
        "recording",
        "errors",
        "setSelectedDurations",
        "selectedDuration",
        "maxLengthInput"
    ],
    data() {
        return {
            durationOptions: [
                { value: "1", text: "< 15s" },
                { value: "2", text: "15s a 30s" },
                { value: "3", text: "30s a 50s" },
                { value: "4", text: "50s a 1m" },
                { value: "5", text: "1m a 5m" },
                { value: "6", text: "5m a 7m" },
                { value: "7", text: "> 7m" }
            ]
        };
    },
    methods: {
        getSelectedItem(myarg) {
            console.log(myarg);
            this.$emit("setSelectedDurations", myarg);
        }
    }
};
</script>
