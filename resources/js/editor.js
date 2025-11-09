// Page Block Editor
class PageEditor {
    constructor() {
        this.blocks = [];
        this.blockSchemas = {};
        this.init();
    }

    async init() {
        await this.prepareCsrf();
        await this.loadBlockSchemas();
        this.bindEvents();
        // Auto-load page on init
        await this.loadPage();
    }

    async prepareCsrf() {
        try {
            await fetch(window.csrfCookieUrl, {
                method: 'GET',
                credentials: 'same-origin',
            });
        } catch (error) {
            console.error('Unable to prepare CSRF cookies:', error);
        }
    }

    async loadBlockSchemas() {
        try {
            const response = await fetch(`${window.apiBaseUrl}/blocks/schema`, {
                credentials: 'same-origin',
                headers: {
                    ...this.getAuthHeaders(),
                },
            });

            if (response.ok) {
                this.blockSchemas = await response.json();
                console.log('Block schemas loaded:', this.blockSchemas);
            } else {
                console.error('Failed to load block schemas');
            }
        } catch (error) {
            console.error('Block schema load error:', error);
        }
    }

    bindEvents() {
        document.getElementById('loadBtn').addEventListener('click', () => this.loadPage());
        document.getElementById('saveBtn').addEventListener('click', () => this.savePage());
        document.getElementById('addBlockBtn').addEventListener('click', () => this.showAddBlockModal());
    }

    async loadPage() {
        try {
            const response = await fetch(`${window.apiBaseUrl}/pages/${window.pageSlug}`, {
                credentials: 'same-origin',
                headers: {
                    ...this.getAuthHeaders(),
                },
            });

            if (response.ok) {
                const data = await response.json();
                this.blocks = data.blocks || [];
                this.renderBlocks();
                this.updatePreview();
            } else {
                const error = await response.json().catch(() => ({}));
                console.error('Failed to load page', error);
            }
        } catch (error) {
            console.error('Load error:', error);
        }
    }

    async savePage() {
        try {
            const pageData = {
                blocks: this.blocks
            };

            const response = await fetch(`${window.apiBaseUrl}/pages/${window.pageSlug}`, {
                method: 'PUT',
                headers: {
                    ...this.getAuthHeaders(true),
                },
                credentials: 'same-origin',
                body: JSON.stringify(pageData),
            });

            if (response.ok) {
                alert('Page saved successfully!');
            } else {
                const error = await response.json().catch(() => ({}));
                console.error('Failed to save page', error);
            }
        } catch (error) {
            console.error('Save error:', error);
        }
    }

    showAddBlockModal() {
        const blockTypes = Object.keys(this.blockSchemas);
        const blockType = prompt(`Enter block type (${blockTypes.join(', ')}):`);
        if (blockType && blockTypes.includes(blockType)) {
            this.addBlock(blockType);
        } else if (blockType) {
            alert(`Invalid block type. Available: ${blockTypes.join(', ')}`);
        }
    }

    addBlock(type) {
        const newBlock = {
            type: type,
            data: this.getDefaultBlockData(type)
        };

        this.blocks.push(newBlock);
        this.renderBlocks();
        this.updatePreview();
    }

    getDefaultBlockData(type) {
        const schema = this.blockSchemas[type];
        if (!schema || !schema.schema) return {};

        const data = {};
        Object.keys(schema.schema).forEach(field => {
            const fieldConfig = schema.schema[field];
            data[field] = fieldConfig.default || (fieldConfig.type === 'array' ? [] : '');
        });

        return data;
    }

    renderBlocks() {
        const container = document.getElementById('blocks-editor');
        container.innerHTML = '';

        this.blocks.forEach((block, index) => {
            const blockElement = this.createBlockElement(block, index);
            container.appendChild(blockElement);
        });
    }

    createBlockElement(block, index) {
        const template = document.getElementById(`${block.type}-block-template`);
        if (!template) return document.createElement('div');

        const clone = template.content.cloneNode(true);
        const blockElement = clone.querySelector('.block-item');

        // Bind events
        const removeBtn = blockElement.querySelector('.remove-block');
        removeBtn.addEventListener('click', () => this.removeBlock(index));

        // Populate data
        this.populateBlockData(blockElement, block);

        // Bind input events
        this.bindBlockInputs(blockElement, block, index);

        return blockElement;
    }

    populateBlockData(element, block) {
        switch (block.type) {
            case 'hero':
                element.querySelector('.block-title').value = block.data.title || '';
                element.querySelector('.block-subtitle').value = block.data.subtitle || '';
                break;
            case 'text':
                element.querySelector('.block-content').value = block.data.content || '';
                break;
            case 'features':
                element.querySelector('.block-title').value = block.data.title || '';
                this.renderFeatures(element, block.data.items || []);
                break;
        }
    }

    bindBlockInputs(element, block, index) {
        element.addEventListener('input', (e) => {
            this.updateBlockData(index, e.target);
        });

        // Special handling for features
        if (block.type === 'features') {
            const addFeatureBtn = element.querySelector('.add-feature');
            if (addFeatureBtn) {
                addFeatureBtn.addEventListener('click', () => this.addFeature(index));
            }
        }
    }

    updateBlockData(blockIndex, target) {
        const block = this.blocks[blockIndex];

        switch (block.type) {
            case 'hero':
                if (target.classList.contains('block-title')) {
                    block.data.title = target.value;
                } else if (target.classList.contains('block-subtitle')) {
                    block.data.subtitle = target.value;
                }
                break;
            case 'text':
                if (target.classList.contains('block-content')) {
                    block.data.content = target.value;
                }
                break;
            case 'features':
                if (target.classList.contains('block-title')) {
                    block.data.title = target.value;
                }
                break;
        }

        this.updatePreview();
    }

    renderFeatures(element, items) {
        const featuresList = element.querySelector('.features-list');
        featuresList.innerHTML = '';

        items.forEach((item, itemIndex) => {
            const featureElement = this.createFeatureElement(item, itemIndex);
            featuresList.appendChild(featureElement);
        });
    }

    createFeatureElement(item, itemIndex) {
        const template = document.getElementById('feature-item-template');
        const clone = template.content.cloneNode(true);
        const featureElement = clone.querySelector('.feature-item');

        featureElement.querySelector('.feature-title').value = item.title || '';
        featureElement.querySelector('.feature-description').value = item.description || '';

        const removeBtn = featureElement.querySelector('.remove-feature');
        removeBtn.addEventListener('click', () => this.removeFeature(itemIndex));

        return featureElement;
    }

    addFeature(blockIndex) {
        const block = this.blocks[blockIndex];
        if (!block.data.items) block.data.items = [];
        block.data.items.push({ title: '', description: '' });
        this.renderBlocks();
        this.updatePreview();
    }

    removeFeature(itemIndex) {
        // This is simplified - in a real implementation you'd need to track which block this belongs to
        console.log('Remove feature:', itemIndex);
    }

    removeBlock(index) {
        this.blocks.splice(index, 1);
        this.renderBlocks();
        this.updatePreview();
    }

    updatePreview() {
        const preview = document.getElementById('preview');
        // Simple preview - in a real implementation you'd render the blocks
        preview.innerHTML = `<pre>${JSON.stringify(this.blocks, null, 2)}</pre>`;
    }

    getAuthHeaders(includeJson = false) {
        const headers = {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken,
        };

        const xsrfToken = this.getXsrfToken();
        if (xsrfToken) {
            headers['X-XSRF-TOKEN'] = xsrfToken;
        }

        if (includeJson) {
            headers['Content-Type'] = 'application/json';
        }

        return headers;
    }

    getXsrfToken() {
        const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
        if (!match) {
            return null;
        }

        try {
            return decodeURIComponent(match[1]);
        } catch (error) {
            console.error('Failed to decode XSRF token', error);
            return match[1];
        }
    }
}

// Initialize editor when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new PageEditor();
});
