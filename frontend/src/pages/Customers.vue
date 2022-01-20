<template>
    <div class="row">
        <button class="btn" @click="add">Add</button>
        <modal name="addmodal" transition="pop-out" :width="modalWidth" :focus-trap="true" :height="400">
            <div class="box">
                <div class="partition" id="partition-register">
                    <div class="partition-title">CREATE CUSTOMER</div>
                    <div class="partition-form">
                        <form autocomplete="false">
                            <input id="n-email" type="text" placeholder="Nama" v-model="form.nama">
                            <input id="n-email" type="text" placeholder="Contact" v-model="form.contact">
                            <input id="n-email" type="text" placeholder="Alamat" v-model="form.alamat">
                            <input id="n-email" type="text" placeholder="Diskon" v-model="form.diskon">
                            <select id="n-tipe" name="tipediskon" v-model="form.tipe_diskon" placeholder="Tipe Diskon">
                                      <option>fix</option>
                                      <option>percent</option>
                                    </select>
                            <input id="n-email" type="file" placeholder="KTP" accept="image/jpeg/*" v-on:change="handlefile">
                        </form>
    
                        <div class="col-md-12 text-center">
                            <button class="btn btn-info" @click="submit">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </modal>
        <div class="col-12">
            <card :title="title">
                <div slot="raw-content" class="table-responsive">
                    <table class="table">
                        <thead>
                            <slot name="columns">
                                <th v-for="column in tableColumns" :key="column">{{ column }}</th>
                            </slot>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in tableData" :key="index">
                                <td>{{item.id}}</td>
                                <td>{{item.nama}}</td>
                                <td>{{item.contact}}</td>
                                <td>{{item.alamat}}</td>
                                <td>{{item.diskon}}</td>
                                <td>{{item.tipe_diskon}}</td>
                                <td><img v-bind:src="apiurl+item.ktp" style="width:70px"></td>
                                <td>{{new Date(item.updated_at).toLocaleString('en-GB')}}</td>
                                <td>
                                    <p-button type="info" outline icon><i class="fa fa-pencil" @click="edit(item.id)"></i></p-button>
                                    <p-button type="danger" outline icon><i class="fa fa-trash" @click="remove(item.id)"></i></p-button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </card>
        </div>
    </div>
</template>

<script>
const tableColumns = ["Id", "Nama", "Contact", "Alamat", "Diskon", "Tipe Diskon", "KTP", "updated", "action"];
const MODAL_WIDTH = 656
const apiurl = "http://127.0.0.1:8000";

let tableData = [];
let stateform = 'add';


import axios from 'axios';
import VModal from 'vue-js-modal'



// import {onMounted, ref} from 'vue';

export default {
    name: 'MyComponent',
    mounted() {
        this.getData();
    },
    data() {
        return {
            form: {
                nama: '',
                alamat: '',
                contact: '',
                diskon: '',
                tipe_diskon: 'fix',
                ktp: '',
            },
            tableData,
            tableColumns,
            apiurl,
            title: "Customers",
            modalWidth: MODAL_WIDTH
        };
    },
    created() {
        this.modalWidth =
            window.innerWidth < MODAL_WIDTH ? MODAL_WIDTH / 2 : MODAL_WIDTH
    },
    methods: {
        getData() {
            axios.get(apiurl + '/api/customers')
                .then((result) => {
                    this.tableData = result.data.data;
                    console.log(result);
                }).catch((err) => {
                    console.log(err);
                });
        },
        handlefile(e) {
            const selectedImage = e.target.files[0];
            this.createBase64(selectedImage);
        },
        createBase64(image) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.form.ktp = e.target.result;
            };
            reader.readAsDataURL(image);
        },
        add() {
          stateform = 'add';
            this.form = {
                nama: '',
                alamat: '',
                contact: '',
                diskon: '',
                tipe_diskon: 'fix',
                ktp: '',
            };
            this.$modal.show('addmodal');
            
        },
        submit() {
            let url = apiurl + '/api/customers';
            let method = "POST";

            console.log(stateform);
            if (stateform == 'update') {
                url += '/' + this.form.id;
                method = "PUT";
            }

            axios({
                    method: method,
                    url: url,
                    data: this.form
                })
                .then((res) => {
                    alert(res.data.message);
                    this.getData();
                })
                .catch((error) => {
                    // error.response.status Check status code
                }).finally(() => {
                    //Perform action in always
                });
        },
        edit(id) {
            stateform = 'update';
            axios.get(apiurl + '/api/customers/' + id)
                .then((result) => {
                    this.form = result.data.data;
                    this.$modal.show('addmodal');
                    console.log(result);
                }).catch((err) => {
                    console.log(err);
                });
        },
        remove(id) {
            axios.delete(apiurl + '/api/customers/' + id)
                .then((result) => {
                    alert(result.data.message)
                    console.log(result);
                    this.getData();
                }).catch((err) => {
                    console.log(err);
                });
        }
    }
};
</script>

<style lang="scss">

</style>
