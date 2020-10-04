/*
 * Copyright 2020 Benjamin Kleiner
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies
 * or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO
 * THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR
 * THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

const addDisplayAmountToTags = datum => {
    datum.displayAmount = datum.amount > 1100 ? (datum.amount / 1000.0).toFixed(1) + 'k' : datum.amount;
    return datum;
};

const addFilterTagsToUri = (uri, filterTags) => {
    filterTags.filter(tag => !!tag).forEach(tag => {
        switch (tag[0]) {
            case '-':
                uri.searchParams.append('tags[none][]', tag.substr(1));
                break;
            case '+':
                uri.searchParams.append('tags[all][]', tag.substr(1));
                break;
            default:
                uri.searchParams.append('tags[all][]', tag.toString());
                break;
        }
    });
};

class Client {
    constructor(baseUri) {
        this.baseUri = baseUri;
    }

    async getTagsForPost(uuid) {
        const uri = new URL('api/posts/' + uuid + '/tags', this.baseUri);
        return fetch(uri.toString())
            .then(response => response.json())
            .then(data => data.map(addDisplayAmountToTags))
            ;
    }

    async getTags(filterTags = []) {
        const uri = new URL('api/posts/tags', this.baseUri);
        addFilterTagsToUri(uri, filterTags);
        return fetch(uri.toString())
            .then(response => response.json())
            .then(data => data.map(addDisplayAmountToTags))
            ;
    }

    async getPosts(filterTags = [], page = 1) {
        const uri = new URL('api/posts', this.baseUri);
        uri.searchParams.set('page', page.toString());
        addFilterTagsToUri(uri, filterTags);
        return fetch(uri.toString())
            .then(response => response.json())
            ;
    }

    async getPost(uuid) {
        const uri = new URL('api/posts/' + uuid, this.baseUri);
        return fetch(uri.toString())
            .then(response => response.json());
    }
}

export default Client;