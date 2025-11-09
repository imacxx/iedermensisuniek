// Frontend Inline WYSIWYG Editor for Pages
class InlinePageEditor {
    constructor() {
        this.blocks = window.pageData?.blocks || [];
        this.pageSlug = window.pageData?.slug;
        this.apiUrl = window.pageData?.apiUrl;
        this.csrfCookieUrl = window.pageData?.csrfCookieUrl;
        this.blockSchemas = {};
        this.currentEditingIndex = null;
        this.hasChanges = false;
        this.quillEditor = null;
        this.sortable = null;
        
        this.init();
    }

    async init() {
        await this.prepareCsrf();
        await this.loadBlockSchemas();
        this.initDragAndDrop();
        this.initResizable();
        this.bindEvents();
        console.log('Inline WYSIWYG editor initialized with', Object.keys(this.blockSchemas).length, 'block types');
        
        if (Object.keys(this.blockSchemas).length === 0) {
            console.error('No block schemas loaded! Editor may not work correctly.');
            this.updateStatus('Error: Block schemas not loaded');
        }
    }

    async prepareCsrf() {
        try {
            console.log('Fetching CSRF cookie from:', this.csrfCookieUrl);
            const response = await fetch(this.csrfCookieUrl, {
                method: 'GET',
                credentials: 'same-origin',
            });
            console.log('CSRF cookie response:', response.status);
            
            if (!response.ok) {
                console.error('Failed to get CSRF cookie');
            }
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
                console.log('Block schemas loaded:', this.blockSchemas);
            } else {
                console.error('Failed to load block schemas. Status:', response.status);
            }
        } catch (error) {
            console.error('Failed to load block schemas:', error);
        }
    }

    initDragAndDrop() {
        const contentArea = document.getElementById('editable-content');
        if (contentArea && window.Sortable) {
            this.sortable = new Sortable(contentArea, {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'sortable-ghost',
                dragClass: 'sortable-drag',
                onEnd: (evt) => {
                    const oldIndex = evt.oldIndex;
                    const newIndex = evt.newIndex;
                    
                    if (oldIndex !== newIndex) {
                        // Update blocks array
                        const movedBlock = this.blocks.splice(oldIndex, 1)[0];
                        this.blocks.splice(newIndex, 0, movedBlock);
                        
                        this.hasChanges = true;
                        this.updateStatus('Blokken opnieuw geordend - klik op Wijzigingen Opslaan');
                    }
                }
            });
            
            console.log('Drag and drop initialized');
        }
    }

    initResizable() {
        // Initialize resize functionality for blocks
        document.querySelectorAll('.resizable-block').forEach(block => {
            const resizeHandle = block.querySelector('.resize-handle');
            if (!resizeHandle) return;

            let startY, startHeight, isResizing = false;
            const blockIndex = parseInt(block.dataset.blockIndex);

            resizeHandle.addEventListener('mousedown', (e) => {
                isResizing = true;
                startY = e.clientY;
                startHeight = parseInt(document.defaultView.getComputedStyle(block).height, 10);
                e.preventDefault();
                
                document.body.style.cursor = 'nwse-resize';
                block.style.transition = 'none';
            });

            document.addEventListener('mousemove', (e) => {
                if (!isResizing) return;
                
                const height = startHeight + (e.clientY - startY);
                if (height > 50) { // Minimum height
                    block.style.height = height + 'px';
                }
            });

            document.addEventListener('mouseup', () => {
                if (isResizing) {
                    isResizing = false;
                    document.body.style.cursor = '';
                    block.style.transition = '';
                    
                    // Store the height in the block data
                    const finalHeight = parseInt(block.style.height);
                    if (this.blocks[blockIndex]) {
                        if (!this.blocks[blockIndex].data) {
                            this.blocks[blockIndex].data = {};
                        }
                        this.blocks[blockIndex].data.custom_height = finalHeight;
                    }
                    
                    this.hasChanges = true;
                    this.updateStatus('Blok aangepast - klik op Wijzigingen Opslaan');
                    console.log('Block resized:', blockIndex, 'height:', finalHeight);
                }
            });
        });
        
        console.log('Resizable blocks initialized');
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

        modalTitle.textContent = 'Nieuw Blok Toevoegen';
        
        // Show block type selector with previews
        const blockTypes = Object.keys(this.blockSchemas);
        modalContent.innerHTML = `
            <div class="space-y-4">
                <label class="block text-sm font-medium text-neutral-700 mb-3">Kies een bloktype om toe te voegen:</label>
                <div class="grid grid-cols-2 gap-4">
                    ${blockTypes.map(type => {
                        const schema = this.blockSchemas[type];
                        return `
                            <button type="button" 
                                    class="block-type-selector p-4 border-2 border-neutral-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-all text-left group"
                                    data-block-type="${type}">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200">
                                        ${this.getBlockIcon(type)}
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-neutral-900 group-hover:text-blue-600">${this.translateBlockName(type)}</h3>
                                        <p class="text-xs text-neutral-500 mt-1">${this.translateBlockDescription(type)}</p>
                                    </div>
                                </div>
                            </button>
                        `;
                    }).join('')}
                </div>
            </div>
        `;

        // Add click handlers for block type selection
        modalContent.querySelectorAll('.block-type-selector').forEach(btn => {
            btn.addEventListener('click', () => {
                const type = btn.dataset.blockType;
                this.currentEditingIndex = -1;
                modalTitle.textContent = `${this.translateBlockName(type)} Toevoegen`;
                this.showBlockEditor(type, this.getDefaultBlockData(type));
            });
        });

        modal.classList.remove('hidden');
    }

    translateBlockName(type) {
        const translations = {
            'hero': 'Hero Sectie',
            'text': 'Tekst',
            'image': 'Afbeelding',
            'gallery': 'Galerij',
            'features': 'Kenmerken',
            'two_column': 'Twee Kolommen',
            'cta': 'Call-to-Action'
        };
        return translations[type] || type;
    }

    translateBlockDescription(type) {
        const translations = {
            'hero': 'Grote hero sectie met titel en ondertitel',
            'text': 'Rijke tekst inhoud blok',
            'image': 'Enkele afbeelding met optioneel bijschrift',
            'gallery': 'Grid van afbeeldingen',
            'features': 'Lijst van kenmerken met titels en beschrijvingen',
            'two_column': 'Inhoud in twee kolommen',
            'cta': 'Call-to-action sectie met knop'
        };
        return translations[type] || '';
    }

    getBlockIcon(type) {
        const icons = {
            'hero': '<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>',
            'text': '<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>',
            'image': '<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
            'gallery': '<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
            'features': '<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            'two_column': '<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 4H5a2 2 0 00-2 2v14a2 2 0 002 2h4m10-18h4a2 2 0 012 2v14a2 2 0 01-2 2h-4m-6-18v18"/></svg>',
            'cta': '<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>',
        };
        return icons[type] || '<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>';
    }

    editBlock(index) {
        if (Object.keys(this.blockSchemas).length === 0) {
            alert('Editor is nog niet klaar. Blokschema\'s worden nog geladen. Probeer het over een moment opnieuw.');
            return;
        }

        const block = this.blocks[index];
        this.currentEditingIndex = index;
        
        const modal = document.getElementById('block-edit-modal');
        const modalTitle = document.getElementById('modal-title');
        
        modalTitle.textContent = `${this.translateBlockName(block.type)} Bewerken`;
        
        this.showBlockEditor(block.type, block.data);
        modal.classList.remove('hidden');
    }

    showBlockEditor(blockType, blockData) {
        const schema = this.blockSchemas[blockType];
        if (!schema) {
            console.error('Schema not found for block type:', blockType);
            return;
        }

        const modalContent = document.getElementById('modal-content');
        let html = `<div class="space-y-6" data-block-type="${blockType}">`;

        for (const [field, config] of Object.entries(schema.schema)) {
            const value = blockData[field] !== undefined ? blockData[field] : (config.default || '');
            
            html += `<div class="form-group">`;
            html += `<label class="block text-sm font-semibold text-neutral-900 mb-2">${this.formatLabel(field)}</label>`;

            if (config.type === 'html' || field.includes('content')) {
                // Use Quill WYSIWYG editor for HTML content
                html += `<div id="quill-${field}" class="bg-white border border-neutral-300 rounded-lg" style="min-height: 200px;"></div>`;
                html += `<input type="hidden" name="${field}" value="">`;
                html += `<p class="mt-2 text-xs text-neutral-500">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Gebruik de werkbalk om tekst op te maken, links toe te voegen, lijsten en meer
                </p>`;
                
                // Store value to initialize Quill later
                setTimeout(() => this.initQuillEditor(field, value), 100);
            } else if (config.type === 'array') {
                html += this.renderArrayField(field, value, config);
            } else if (config.options) {
                html += `<select name="${field}" class="w-full border-2 border-neutral-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">`;
                config.options.forEach(opt => {
                    html += `<option value="${opt}" ${value === opt ? 'selected' : ''}>${opt}</option>`;
                });
                html += `</select>`;
            } else if (field.includes('image') || field.includes('url') || field.includes('background')) {
                // Image/URL field with preview and drag-drop upload
                html += `<div class="space-y-2">`;
                html += `<input type="text" name="${field}" value="${this.escapeHtml(String(value))}" placeholder="Voer URL in of upload afbeelding" class="w-full border-2 border-neutral-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition image-url-input" data-field="${field}">`;
                if ((field.includes('image') || field.includes('background')) && value) {
                    html += `<div class="mt-2 image-preview-container">
                        <img src="${value}" alt="Voorbeeld" class="max-w-full h-48 object-cover rounded-lg border-2 border-neutral-200" />
                    </div>`;
                }
                html += `
                <div class="flex space-x-2">
                    <input type="file" id="file-input-${field}" accept="image/*" class="hidden" data-field="${field}">
                    <button type="button" class="upload-image-btn flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition" data-field="${field}">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Afbeelding Uploaden
                    </button>
                    <div class="upload-dropzone flex-1 border-2 border-dashed border-neutral-300 rounded-lg px-4 py-2 text-center hover:border-blue-500 transition cursor-pointer bg-neutral-50" data-field="${field}">
                        <svg class="w-6 h-6 inline text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <p class="text-xs text-neutral-500 mt-1">Of sleep afbeelding hier</p>
                    </div>
                </div>`;
                html += `</div>`;
                
                // Bind upload events after render
                setTimeout(() => this.bindImageUploadEvents(field), 100);
            } else {
                html += `<input type="text" name="${field}" value="${this.escapeHtml(String(value))}" class="w-full border-2 border-neutral-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" placeholder="Voer ${this.translateFieldName(field).toLowerCase()} in">`;
            }

            html += `</div>`;
        }

        html += `</div>`;
        modalContent.innerHTML = html;
    }

    initQuillEditor(field, initialContent) {
        const container = document.getElementById(`quill-${field}`);
        if (!container || !window.Quill) return;

        const quill = new Quill(`#quill-${field}`, {
            theme: 'snow',
            placeholder: 'Begin met typen...',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'align': [] }],
                    ['link', 'image'],
                    ['clean']
                ]
            }
        });

        // Set initial content
        if (initialContent) {
            quill.root.innerHTML = initialContent;
        }

        // Store reference for later extraction
        container.quillInstance = quill;
    }

    bindImageUploadEvents(field) {
        const uploadBtn = document.querySelector(`.upload-image-btn[data-field="${field}"]`);
        const fileInput = document.getElementById(`file-input-${field}`);
        const dropZone = document.querySelector(`.upload-dropzone[data-field="${field}"]`);
        const urlInput = document.querySelector(`.image-url-input[data-field="${field}"]`);

        if (uploadBtn && fileInput) {
            uploadBtn.addEventListener('click', () => {
                fileInput.click();
            });

            fileInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    this.uploadImage(file, field, urlInput);
                }
            });
        }

        if (dropZone) {
            dropZone.addEventListener('click', () => {
                fileInput.click();
            });

            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('border-blue-500', 'bg-blue-50');
            });

            dropZone.addEventListener('dragleave', () => {
                dropZone.classList.remove('border-blue-500', 'bg-blue-50');
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-blue-500', 'bg-blue-50');
                
                const file = e.dataTransfer.files[0];
                if (file && file.type.startsWith('image/')) {
                    this.uploadImage(file, field, urlInput);
                }
            });
        }

        // Live preview when URL changes
        if (urlInput) {
            urlInput.addEventListener('input', (e) => {
                const previewContainer = urlInput.closest('.space-y-2').querySelector('.image-preview-container');
                if (e.target.value && (field.includes('image') || field.includes('background'))) {
                    if (previewContainer) {
                        previewContainer.querySelector('img').src = e.target.value;
                    } else {
                        const newPreview = document.createElement('div');
                        newPreview.className = 'mt-2 image-preview-container';
                        newPreview.innerHTML = `<img src="${e.target.value}" alt="Voorbeeld" class="max-w-full h-48 object-cover rounded-lg border-2 border-neutral-200" />`;
                        urlInput.after(newPreview);
                    }
                }
            });
        }
    }

    async uploadImage(file, field, urlInput) {
        const formData = new FormData();
        formData.append('file', file);

        try {
            urlInput.value = 'Uploaden...';
            urlInput.disabled = true;

            const response = await fetch('/api/uploads', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': window.pageData.csrfToken
                },
                body: formData
            });

            if (response.ok) {
                const data = await response.json();
                urlInput.value = data.url || data.path;
                urlInput.disabled = false;
                
                // Trigger preview update
                urlInput.dispatchEvent(new Event('input'));
                
                alert('Afbeelding succesvol geüpload!');
            } else {
                throw new Error('Upload mislukt');
            }
        } catch (error) {
            console.error('Image upload error:', error);
            alert('Fout bij uploaden van afbeelding. Probeer het opnieuw.');
            urlInput.value = '';
            urlInput.disabled = false;
        }
    }

    renderArrayField(fieldName, items, config) {
        let html = `<div class="array-field space-y-3 border-2 border-dashed border-neutral-300 rounded-lg p-4" data-field="${fieldName}">`;
        
        if (Array.isArray(items) && items.length > 0) {
            items.forEach((item, idx) => {
                html += `<div class="array-item bg-neutral-50 border border-neutral-200 rounded-lg p-4" data-item-index="${idx}">`;
                html += `<div class="flex justify-between items-center mb-3">`;
                html += `<span class="text-sm font-semibold text-neutral-700">Item ${idx + 1}</span>`;
                html += `<button type="button" class="remove-array-item text-red-600 hover:text-red-800 font-medium text-sm px-3 py-1 hover:bg-red-50 rounded transition">Verwijderen</button>`;
                html += `</div>`;
                html += `<div class="space-y-2">`;
                
                if (config.item_schema) {
                    for (const [subField, subConfig] of Object.entries(config.item_schema)) {
                        const subValue = item[subField] || '';
                        html += `<div>`;
                        html += `<label class="block text-xs font-medium text-neutral-600 mb-1">${this.translateFieldName(subField)}</label>`;
                        html += `<input type="text" name="${fieldName}[${idx}][${subField}]" value="${this.escapeHtml(subValue)}" class="w-full border border-neutral-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Voer ${this.translateFieldName(subField).toLowerCase()} in">`;
                        html += `</div>`;
                    }
                }
                
                html += `</div></div>`;
            });
        } else {
            html += `<p class="text-sm text-neutral-500 text-center py-4">Nog geen items. Klik op "Item Toevoegen" om te beginnen.</p>`;
        }
        
        html += `<button type="button" class="add-array-item w-full mt-3 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Item Toevoegen
        </button>`;
        html += `</div>`;
        
        // Bind array item events
        setTimeout(() => {
            document.querySelectorAll('.remove-array-item').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    if (confirm('Dit item verwijderen?')) {
                        e.target.closest('.array-item').remove();
                    }
                });
            });
            
            document.querySelectorAll('.add-array-item').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const arrayField = e.target.closest('.array-field');
                    const fieldName = arrayField.dataset.field;
                    const newIndex = arrayField.querySelectorAll('.array-item').length;
                    
                    const newItemHtml = this.createArrayItemHtml(fieldName, newIndex, config);
                    btn.insertAdjacentHTML('beforebegin', newItemHtml);
                    
                    // Re-bind remove event
                    const newItem = arrayField.querySelector(`[data-item-index="${newIndex}"]`);
                    newItem.querySelector('.remove-array-item').addEventListener('click', (e) => {
                        if (confirm('Dit item verwijderen?')) {
                            e.target.closest('.array-item').remove();
                        }
                    });
                });
            });
        }, 0);
        
        return html;
    }

    createArrayItemHtml(fieldName, index, config) {
        let html = `<div class="array-item bg-neutral-50 border border-neutral-200 rounded-lg p-4" data-item-index="${index}">`;
        html += `<div class="flex justify-between items-center mb-3">`;
        html += `<span class="text-sm font-semibold text-neutral-700">Item ${index + 1}</span>`;
        html += `<button type="button" class="remove-array-item text-red-600 hover:text-red-800 font-medium text-sm px-3 py-1 hover:bg-red-50 rounded transition">Verwijderen</button>`;
        html += `</div>`;
        html += `<div class="space-y-2">`;
        
        if (config.item_schema) {
            for (const [subField, subConfig] of Object.entries(config.item_schema)) {
                html += `<div>`;
                html += `<label class="block text-xs font-medium text-neutral-600 mb-1">${this.translateFieldName(subField)}</label>`;
                html += `<input type="text" name="${fieldName}[${index}][${subField}]" value="" class="w-full border border-neutral-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Voer ${this.translateFieldName(subField).toLowerCase()} in">`;
                html += `</div>`;
            }
        }
        
        html += `</div></div>`;
        return html;
    }

    saveBlockFromModal() {
        const modalContent = document.getElementById('modal-content');
        const blockTypeDiv = modalContent.querySelector('[data-block-type]');
        
        if (!blockTypeDiv) {
            alert('Ongeldige blok configuratie');
            return;
        }

        const blockType = blockTypeDiv.dataset.blockType;
        const blockData = this.extractBlockData(modalContent);

        if (this.currentEditingIndex === -1) {
            this.blocks.push({ type: blockType, data: blockData });
            this.updateStatus('Blok toegevoegd - klik op Wijzigingen Opslaan om te publiceren');
        } else {
            this.blocks[this.currentEditingIndex].data = blockData;
            this.updateStatus('Blok bijgewerkt - klik op Wijzigingen Opslaan om te publiceren');
        }

        this.hasChanges = true;
        this.closeModal();
        location.reload(); // Reload to show changes
    }

    extractBlockData(container) {
        const data = {};
        
        // Extract Quill content
        container.querySelectorAll('[id^="quill-"]').forEach(quillContainer => {
            if (quillContainer.quillInstance) {
                const fieldName = quillContainer.id.replace('quill-', '');
                data[fieldName] = quillContainer.quillInstance.root.innerHTML;
            }
        });
        
        // Extract regular inputs
        const inputs = container.querySelectorAll('input:not([type="hidden"]), textarea, select');
        inputs.forEach(input => {
            const name = input.name;
            if (!name) return;

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
        if (confirm('Weet u zeker dat u dit blok wilt verwijderen? Dit kan niet ongedaan worden gemaakt.')) {
            this.blocks.splice(index, 1);
            this.hasChanges = true;
            this.updateStatus('Blok verwijderd - klik op Wijzigingen Opslaan');
            location.reload();
        }
    }

    moveBlock(index, direction) {
        const newIndex = index + direction;
        if (newIndex < 0 || newIndex >= this.blocks.length) return;

        [this.blocks[index], this.blocks[newIndex]] = [this.blocks[newIndex], this.blocks[index]];
        this.hasChanges = true;
        this.updateStatus('Blok verplaatst - klik op Wijzigingen Opslaan');
        location.reload();
    }

    async savePage() {
        const saveBtn = document.getElementById('save-page-btn');
        saveBtn.disabled = true;
        this.updateStatus('Opslaan...');
        
        console.log('Saving blocks:', this.blocks);
        
        try {
            // Get XSRF token from cookie
            const xsrfToken = this.getCookie('XSRF-TOKEN');
            console.log('XSRF-TOKEN cookie:', xsrfToken ? 'present' : 'missing');
            
            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': window.pageData.csrfToken
            };
            
            // Add XSRF token if available (required by Sanctum)
            if (xsrfToken) {
                headers['X-XSRF-TOKEN'] = decodeURIComponent(xsrfToken);
            }
            
            const response = await fetch(this.apiUrl, {
                method: 'PUT',
                credentials: 'include',
                headers: headers,
                body: JSON.stringify({ blocks: this.blocks })
            });

            console.log('Response status:', response.status);
            
            if (response.ok) {
                const data = await response.json();
                console.log('Save successful:', data);
                this.hasChanges = false;
                this.updateStatus('✓ Succesvol opgeslagen!');
                setTimeout(() => location.reload(), 1000);
            } else {
                const errorData = await response.text();
                console.error('Save failed:', response.status, errorData);
                this.updateStatus(`❌ Opslaan mislukt (${response.status}) - probeer het opnieuw`);
                saveBtn.disabled = false;
            }
        } catch (error) {
            console.error('Save error:', error);
            this.updateStatus('❌ Fout - probeer het opnieuw');
            saveBtn.disabled = false;
        }
    }
    
    getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    cancelEditing() {
        if (this.hasChanges && !confirm('Alle niet-opgeslagen wijzigingen negeren?')) {
            return;
        }
        location.reload();
    }

    closeModal() {
        const modal = document.getElementById('block-edit-modal');
        modal.classList.add('hidden');
        this.currentEditingIndex = null;
        this.quillEditor = null;
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
        return this.translateFieldName(str);
    }

    translateFieldName(field) {
        const translations = {
            'title': 'Titel',
            'subtitle': 'Ondertitel',
            'content': 'Inhoud',
            'description': 'Beschrijving',
            'items': 'Items',
            'image_url': 'Afbeelding URL',
            'background_image': 'Achtergrond Afbeelding',
            'alt_text': 'Alt Tekst',
            'caption': 'Bijschrift',
            'images': 'Afbeeldingen',
            'columns': 'Kolommen',
            'left_content': 'Linker Inhoud',
            'right_content': 'Rechter Inhoud',
            'layout': 'Indeling',
            'button_text': 'Knop Tekst',
            'button_url': 'Knop URL',
            'background_color': 'Achtergrondkleur',
            'icon': 'Icoon',
            'url': 'URL',
            'alt': 'Alt',
        };
        return translations[field] || str.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    escapeHtml(text) {
        if (text === null || text === undefined) return '';
        const div = document.createElement('div');
        div.textContent = String(text);
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
