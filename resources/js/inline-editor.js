// Frontend Inline Editor for Pages
class InlinePageEditor {
    constructor() {
        this.blocks = window.pageData?.blocks || [];
        this.pageSlug = window.pageData?.slug;
        this.apiUrl = window.pageData?.apiUrl;
        this.csrfCookieUrl = window.pageData?.csrfCookieUrl;
        this.blockSchemas = {};
        this.currentEditingIndex = null;
        this.hasChanges = false;
        
        this.init();
    }

    async init() {
        await this.prepareCsrf();
        await this.loadBlockSchemas();
        this.bindEvents();
        console.log('Inline editor initialized');
    }

    async prepareCsrf() {
        try {
            await fetch(this.csrfCookieUrl, {
                method: 'GET',
                credentials: 'same-origin',
            });
        } catch (error) {
            console.error('CSRF preparation error:', error);
        }
    }

    async loadBlockSchemas() {
        try {
            const response = await fetch('/api/blocks/schema', {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': window.pageData.csrfToken
                }
            });

            if (response.ok) {
                this.blockSchemas = await response.json();
            }
        } catch (error) {
            console.error('Failed to load block schemas:', error);
        }
    }

    bindEvents() {
        // Toolbar buttons
        document.getElementById('add-block-btn')?.addEventListener('click', () => this.showAddBlockModal());
        document.getElementById('save-page-btn')?.addEventListener('click', () => this.savePage());
        document.getElementById('cancel-editing-btn')?.addEventListener('click', () => this.cancelEditing());
        document.getElementById('close-editor')?.addEventListener('click', () => this.toggleToolbar());

        // Modal buttons
        document.getElementById('close-modal-btn')?.addEventListener('click', () => this.closeModal());
        document.getElementById('modal-cancel-btn')?.addEventListener('click', () => this.closeModal());
        document.getElementById('modal-save-btn')?.addEventListener('click', () => this.saveBlockFromModal());

        // Block controls (delegated)
        document.getElementById('editable-content')?.addEventListener('click', (e) => {
            const blockWrapper = e.target.closest('.block-wrapper');
            if (!blockWrapper) return;

            if (e.target.closest('.edit-block-btn')) {
                const index = parseInt(blockWrapper.dataset.blockIndex);
                this.editBlock(index);
            } else if (e.target.closest('.delete-block-btn')) {
                const index = parseInt(blockWrapper.dataset.blockIndex);
                this.deleteBlock(index);
            } else if (e.target.closest('.move-up-btn')) {
                const index = parseInt(blockWrapper.dataset.blockIndex);
                this.moveBlock(index, -1);
            } else if (e.target.closest('.move-down-btn')) {
                const index = parseInt(blockWrapper.dataset.blockIndex);
                this.moveBlock(index, 1);
            }
        });

        // Warn before leaving with unsaved changes
        window.addEventListener('beforeunload', (e) => {
            if (this.hasChanges) {
                e.preventDefault();
                e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                return e.returnValue;
            }
        });
    }

    showAddBlockModal() {
        const modal = document.getElementById('block-edit-modal');
        const modalTitle = document.getElementById('modal-title');
        const modalContent = document.getElementById('modal-content');

        modalTitle.textContent = 'Add New Block';
        
        // Show block type selector
        const blockTypes = Object.keys(this.blockSchemas);
        modalContent.innerHTML = `
            <div class="space-y-4">
                <label class="block text-sm font-medium text-neutral-700">Select Block Type</label>
                <select id="new-block-type" class="w-full border border-neutral-300 rounded-md px-3 py-2">
                    <option value="">-- Choose a block type --</option>
                    ${blockTypes.map(type => `
                        <option value="${type}">${this.blockSchemas[type].name}</option>
                    `).join('')}
                </select>
                <p class="text-sm text-neutral-500">Select a block type to configure its content.</p>
            </div>
        `;

        // Change handler to show block configuration
        document.getElementById('new-block-type').addEventListener('change', (e) => {
            const type = e.target.value;
            if (type) {
                this.currentEditingIndex = -1; // -1 indicates new block
                this.showBlockEditor(type, this.getDefaultBlockData(type));
            }
        });

        modal.classList.remove('hidden');
    }

    editBlock(index) {
        const block = this.blocks[index];
        this.currentEditingIndex = index;
        
        const modal = document.getElementById('block-edit-modal');
        const modalTitle = document.getElementById('modal-title');
        
        modalTitle.textContent = `Edit ${this.blockSchemas[block.type]?.name || block.type} Block`;
        
        this.showBlockEditor(block.type, block.data);
        modal.classList.remove('hidden');
    }

    showBlockEditor(blockType, blockData) {
        const schema = this.blockSchemas[blockType];
        if (!schema) return;

        const modalContent = document.getElementById('modal-content');
        let html = `<div class="space-y-4" data-block-type="${blockType}">`;

        for (const [field, config] of Object.entries(schema.schema)) {
            const value = blockData[field] || config.default || '';
            
            html += `<div class="form-group">`;
            html += `<label class="block text-sm font-medium text-neutral-700 mb-1">${this.formatLabel(field)}</label>`;

            if (config.type === 'html' || field.includes('content')) {
                html += `<textarea name="${field}" rows="8" class="w-full border border-neutral-300 rounded-md px-3 py-2 font-mono text-sm">${this.escapeHtml(value)}</textarea>`;
            } else if (config.type === 'array') {
                html += this.renderArrayField(field, value, config);
            } else if (config.options) {
                html += `<select name="${field}" class="w-full border border-neutral-300 rounded-md px-3 py-2">`;
                config.options.forEach(opt => {
                    html += `<option value="${opt}" ${value === opt ? 'selected' : ''}>${opt}</option>`;
                });
                html += `</select>`;
            } else {
                html += `<input type="text" name="${field}" value="${this.escapeHtml(value)}" class="w-full border border-neutral-300 rounded-md px-3 py-2">`;
            }

            html += `</div>`;
        }

        html += `</div>`;
        modalContent.innerHTML = html;
    }

    renderArrayField(fieldName, items, config) {
        let html = `<div class="array-field space-y-2" data-field="${fieldName}">`;
        
        if (Array.isArray(items)) {
            items.forEach((item, idx) => {
                html += `<div class="array-item border border-neutral-200 rounded p-3 bg-neutral-50" data-item-index="${idx}">`;
                html += `<div class="flex justify-between items-center mb-2">`;
                html += `<span class="text-sm font-medium text-neutral-600">Item ${idx + 1}</span>`;
                html += `<button type="button" class="remove-array-item text-red-600 hover:text-red-800 text-sm">Remove</button>`;
                html += `</div>`;
                
                if (config.item_schema) {
                    for (const [subField, subConfig] of Object.entries(config.item_schema)) {
                        const subValue = item[subField] || '';
                        html += `<div class="mb-2">`;
                        html += `<label class="block text-xs font-medium text-neutral-600 mb-1">${this.formatLabel(subField)}</label>`;
                        html += `<input type="text" name="${fieldName}[${idx}][${subField}]" value="${this.escapeHtml(subValue)}" class="w-full border border-neutral-300 rounded px-2 py-1 text-sm">`;
                        html += `</div>`;
                    }
                }
                
                html += `</div>`;
            });
        }
        
        html += `<button type="button" class="add-array-item mt-2 bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">Add Item</button>`;
        html += `</div>`;
        
        // Bind array item events
        setTimeout(() => {
            document.querySelectorAll('.remove-array-item').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.target.closest('.array-item').remove();
                });
            });
            
            document.querySelectorAll('.add-array-item').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const arrayField = e.target.closest('.array-field');
                    const fieldName = arrayField.dataset.field;
                    const newIndex = arrayField.querySelectorAll('.array-item').length;
                    
                    const newItemHtml = this.createArrayItemHtml(fieldName, newIndex, config);
                    btn.insertAdjacentHTML('beforebegin', newItemHtml);
                });
            });
        }, 0);
        
        return html;
    }

    createArrayItemHtml(fieldName, index, config) {
        let html = `<div class="array-item border border-neutral-200 rounded p-3 bg-neutral-50" data-item-index="${index}">`;
        html += `<div class="flex justify-between items-center mb-2">`;
        html += `<span class="text-sm font-medium text-neutral-600">Item ${index + 1}</span>`;
        html += `<button type="button" class="remove-array-item text-red-600 hover:text-red-800 text-sm">Remove</button>`;
        html += `</div>`;
        
        if (config.item_schema) {
            for (const [subField, subConfig] of Object.entries(config.item_schema)) {
                html += `<div class="mb-2">`;
                html += `<label class="block text-xs font-medium text-neutral-600 mb-1">${this.formatLabel(subField)}</label>`;
                html += `<input type="text" name="${fieldName}[${index}][${subField}]" value="" class="w-full border border-neutral-300 rounded px-2 py-1 text-sm">`;
                html += `</div>`;
            }
        }
        
        html += `</div>`;
        return html;
    }

    saveBlockFromModal() {
        const modalContent = document.getElementById('modal-content');
        const blockTypeDiv = modalContent.querySelector('[data-block-type]');
        
        if (!blockTypeDiv) {
            alert('Invalid block configuration');
            return;
        }

        const blockType = blockTypeDiv.dataset.blockType;
        const blockData = this.extractBlockData(modalContent);

        if (this.currentEditingIndex === -1) {
            // New block
            this.blocks.push({ type: blockType, data: blockData });
        } else {
            // Update existing
            this.blocks[this.currentEditingIndex].data = blockData;
        }

        this.hasChanges = true;
        this.updateStatus('Changes pending - click Save Changes');
        this.closeModal();
        this.reloadContent();
    }

    extractBlockData(container) {
        const data = {};
        const inputs = container.querySelectorAll('input, textarea, select');
        
        inputs.forEach(input => {
            const name = input.name;
            if (!name) return;

            // Handle array fields
            const arrayMatch = name.match(/(\w+)\[(\d+)\]\[(\w+)\]/);
            if (arrayMatch) {
                const [, fieldName, index, subField] = arrayMatch;
                if (!data[fieldName]) data[fieldName] = [];
                if (!data[fieldName][index]) data[fieldName][index] = {};
                data[fieldName][index][subField] = input.value;
            } else {
                data[name] = input.value;
            }
        });

        return data;
    }

    deleteBlock(index) {
        if (confirm('Are you sure you want to delete this block?')) {
            this.blocks.splice(index, 1);
            this.hasChanges = true;
            this.updateStatus('Block deleted - click Save Changes');
            this.reloadContent();
        }
    }

    moveBlock(index, direction) {
        const newIndex = index + direction;
        if (newIndex < 0 || newIndex >= this.blocks.length) return;

        [this.blocks[index], this.blocks[newIndex]] = [this.blocks[newIndex], this.blocks[index]];
        this.hasChanges = true;
        this.updateStatus('Block moved - click Save Changes');
        this.reloadContent();
    }

    async savePage() {
        this.updateStatus('Saving...');
        
        try {
            const response = await fetch(this.apiUrl, {
                method: 'PUT',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': window.pageData.csrfToken
                },
                body: JSON.stringify({ blocks: this.blocks })
            });

            if (response.ok) {
                this.hasChanges = false;
                this.updateStatus('Saved successfully!');
                setTimeout(() => location.reload(), 1000);
            } else {
                this.updateStatus('Save failed - please try again');
            }
        } catch (error) {
            console.error('Save error:', error);
            this.updateStatus('Error saving - please try again');
        }
    }

    cancelEditing() {
        if (this.hasChanges && !confirm('Discard unsaved changes?')) {
            return;
        }
        location.reload();
    }

    reloadContent() {
        const content = document.getElementById('editable-content');
        // This is a simplified reload - in production you'd re-render from blocks
        // For now, just mark as changed
        this.updateStatus(`${this.blocks.length} blocks - unsaved changes`);
    }

    closeModal() {
        document.getElementById('block-edit-modal').classList.add('hidden');
        this.currentEditingIndex = null;
    }

    toggleToolbar() {
        const toolbar = document.getElementById('editor-toolbar');
        toolbar.style.display = toolbar.style.display === 'none' ? 'block' : 'none';
    }

    updateStatus(message) {
        const status = document.getElementById('editor-status');
        if (status) {
            status.textContent = message;
        }
    }

    getDefaultBlockData(type) {
        const schema = this.blockSchemas[type];
        if (!schema) return {};

        const data = {};
        for (const [field, config] of Object.entries(schema.schema)) {
            data[field] = config.default || (config.type === 'array' ? [] : '');
        }
        return data;
    }

    formatLabel(str) {
        return str.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        if (window.pageData?.canEdit) {
            new InlinePageEditor();
        }
    });
} else {
    if (window.pageData?.canEdit) {
        new InlinePageEditor();
    }
}

