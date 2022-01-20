<template>
    <div class="row">
        <button class="btn" @click="add">Add</button>
        <modal name="addmodal" transition="pop-out" :width="modalWidth" :focus-trap="true" :height="400">
            <div class="box">
                <div class="partition" id="partition-register">
                    <div class="partition-title">FORM ITEM</div>
                    <div class="partition-form">
                        <form autocomplete="false">
                            <input id="n-email" type="text" placeholder="Nama" v-model="form.nama_item">
                            <select id="n-tipe" name="unit" v-model="form.unit" placeholder="Unit">
                                                      <option selected value=''>Unit</option>
                                                      <option>pcs</option>
                                                      <option>kg</option>
                                                    </select>
                            <input id="n-stok" type="text" placeholder="Stok" v-model="form.stok">
                            <input id="n-harga_satuan" type="text" placeholder="Harga" v-model="form.harga_satuan">
                            <input id="n-barang" type="file" placeholder="Foto Barang" accept="image/jpeg/*" v-on:change="handlefile">
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
                                <td>{{item.nama_item}}</td>
                                <td>{{item.unit}}</td>
                                <td>{{item.stok}}</td>
                                <td>{{item.harga_satuan}}</td>
                                <td><img v-bind:src="apiurl+item.barang" style="width:70px"></td>
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
import axios from 'axios';

const tableColumns = ["Id", "Nama", "Unit", "Stok", "Harga", "Gambar", "updated", "action"];
const apiurl = "http://127.0.0.1:8000";
const MODAL_WIDTH = 656

let tableData = [];
let stateform = 'add';




export default {

    mounted() {

        this.getData();
    },
    data() {
        return {
            form: {
                nama_item: '',
                unit: 'pcs',
                stok: '',
                harga_satuan: '',
                barang: '',
            },
            tableData,
            tableColumns,
            title: "Items",
            modalWidth: MODAL_WIDTH,
            apiurl
        };
    },
    created() {
        this.modalWidth =
            window.innerWidth < MODAL_WIDTH ? MODAL_WIDTH / 2 : MODAL_WIDTH
    },
    methods: {
        handlefile(e) {
            const selectedImage = e.target.files[0];
            this.createBase64(selectedImage);
        },
        createBase64(image) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.form.barang = e.target.result;
            };
            reader.readAsDataURL(image);
        },
        add() {
            stateform = 'add';
            this.form = {
                nama_item: '',
                unit: 'pcs',
                stok: '',
                harga_satuan: '',
                barang: '',
            };
            this.$modal.show('addmodal');
            
        },
        getData() {
            axios.get(apiurl + '/api/items')
                .then((result) => {
                    this.tableData = result.data.data;
                    console.log(result);
                }).catch((err) => {
                    console.log(err);
                });
        },
        submit() {
            let url = apiurl + '/api/items';
            let method = "POST";

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
            axios.get(apiurl + '/api/items/' + id)
                .then((result) => {
                    this.form = result.data.data;
                    this.$modal.show('addmodal');
                    console.log(result);
                }).catch((err) => {
                    console.log(err);
                });
        },
        remove(id) {
            console.log(id);
            axios.delete(apiurl + '/api/items/' + id)
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

<style>

</style>
