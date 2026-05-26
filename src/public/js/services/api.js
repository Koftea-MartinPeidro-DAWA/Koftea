import axios from 'axios';

const PRODUCTS  = '/productes';
const COMMENTS  = '/comentarios';
const USERS     = '/users';

const api = axios.create({
    baseURL: import.meta.env?.VITE_API_BASE_URL ?? 'http://localhost:3000',
    headers: { 'Content-Type': 'application/json' },
});

// ── Products ──────────────────────────────────────────────────────────────────

export function getProducts(params = {}) {
    return api.get(PRODUCTS, { params }).then(r => r.data);
}

export function getProduct(id) {
    return api.get(PRODUCTS, { params: { ID: id } }).then(r => r.data[0] ?? null);
}

export function getProductsByCategory(category) {
    return getProducts({ Categoria: category });
}

export function searchProducts(query) {
    return getProducts({ q: query });
}

// ── Comments ─────────────────────────────────────────────────────────────────

export function getComments(productoId) {
    return api.get(COMMENTS, { params: { producto_id: productoId } }).then(r => r.data);
}

export function addComment(data) {
    return api.post(COMMENTS, data).then(r => r.data);
}

export function updateComment(id, data) {
    return api.patch(`${COMMENTS}/${id}`, data).then(r => r.data);
}

export function deleteComment(id) {
    return api.delete(`${COMMENTS}/${id}`).then(r => r.data);
}

// ── Users ─────────────────────────────────────────────────────────────────────

export function getUsers(params = {}) {
    return api.get(USERS, { params }).then(r => r.data);
}

export function getUser(id) {
    return api.get(`${USERS}/${id}`).then(r => r.data);
}

export function createUser(data) {
    return api.post(USERS, data).then(r => r.data);
}

export function updateUser(id, data) {
    return api.patch(`${USERS}/${id}`, data).then(r => r.data);
}
