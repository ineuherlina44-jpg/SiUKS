import './bootstrap';

document.querySelectorAll('.student-app, .student-stock-app, .student-history-app, .pmr-app, .wali-app, .siuks-app').forEach((app) => {
    const isStudentApp = app.matches('.student-app, .student-stock-app, .student-history-app');
    const toggle = app.querySelector(isStudentApp
        ? '[data-student-sidebar-toggle]'
        : '.pmr-sidebar-toggle, .wali-sidebar-toggle, .siuks-sidebar-toggle');

    if (!toggle) {
        return;
    }

    const storageKey = isStudentApp
        ? 'siuks-student-sidebar-collapsed'
        : app.classList.contains('pmr-app')
            ? 'siuks-pmr-sidebar-collapsed'
            : app.classList.contains('wali-app')
                ? 'siuks-wali-sidebar-collapsed'
                : 'siuks-admin-sidebar-collapsed';
    const setCollapsed = (collapsed) => {
        app.classList.toggle('sidebar-collapsed', collapsed);
        toggle.setAttribute('aria-expanded', String(!collapsed));
        toggle.setAttribute('aria-label', collapsed ? 'Tampilkan navigasi' : 'Sembunyikan navigasi');
        toggle.title = collapsed ? 'Tampilkan navigasi' : 'Sembunyikan navigasi';
        if (isStudentApp) {
            toggle.textContent = collapsed ? '☰' : '×';
        }
    };

    const mobileSidebar = window.matchMedia('(max-width: 640px)');
    const savedState = localStorage.getItem(storageKey);
    setCollapsed(isStudentApp && mobileSidebar.matches
        ? true
        : savedState === null ? mobileSidebar.matches : savedState === 'true');
    toggle.addEventListener('click', () => {
        const collapsed = !app.classList.contains('sidebar-collapsed');
        setCollapsed(collapsed);
        if (!isStudentApp || !mobileSidebar.matches) {
            localStorage.setItem(storageKey, String(collapsed));
        }
    });

    if (isStudentApp) {
        app.querySelector('[data-student-sidebar-backdrop]')?.addEventListener('click', () => {
            setCollapsed(true);
        });
        mobileSidebar.addEventListener('change', (event) => {
            setCollapsed(event.matches || localStorage.getItem(storageKey) === 'true');
        });
    }
});

document.querySelectorAll('[data-sos-form]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm('Kirim sinyal SOS kepada Petugas PMR? Gunakan hanya saat membutuhkan bantuan UKS.')) {
            event.preventDefault();
            return;
        }

        const button = form.querySelector('button[type="submit"]');
        if (button) {
            button.disabled = true;
            button.textContent = 'Mengirim sinyal...';
        }
    });
});

document.querySelectorAll('#student_id').forEach((select) => {
    const form = select.closest('form');
    const fields = {
        name: form.querySelector('#student_name'),
        nisn: form.querySelector('#student_nisn'),
        class: form.querySelector('#student_class'),
        gender: form.querySelector('#student_gender'),
        allergies: form.querySelector('#student_allergies'),
    };

    const updateStudentDetails = () => {
        const option = select.selectedOptions[0];

        fields.name.value = option?.dataset.name || '';
        fields.nisn.value = option?.dataset.nisn || '';
        fields.class.value = option?.dataset.class || '';
        fields.gender.value = option?.dataset.gender || '';
        fields.allergies.value = option?.dataset.allergies || (select.value ? 'Tidak ada riwayat alergi yang dicatat.' : '');
    };

    select.addEventListener('change', updateStudentDetails);
    updateStudentDetails();

    form.addEventListener('reset', () => {
        requestAnimationFrame(updateStudentDetails);
    });
});

document.querySelectorAll('.wali-app').forEach((app) => {
    const createDialog = app.querySelector('#student-create-dialog');
    const editDialog = app.querySelector('#student-edit-dialog');

    if (!createDialog || !editDialog) {
        return;
    }

    const setDialogQuery = (key, value) => {
        const url = new URL(window.location.href);
        if (value) {
            url.searchParams.set(key, value);
        } else {
            url.searchParams.delete(key);
        }
        window.history.replaceState({}, '', url);
    };

    const openCreateDialog = () => {
        setDialogQuery('create', '1');
        createDialog.showModal();
    };

    const openEditDialog = (button, restoreOldInput = false) => {
        const form = editDialog.querySelector('[data-student-form]');
        const useOldInput = restoreOldInput && app.dataset.validationErrors === 'true';
        form.action = button.dataset.url;
        form.querySelector('[name="name"]').value = useOldInput ? form.dataset.oldName : button.dataset.name;
        form.querySelector('[name="nisn"]').value = useOldInput ? form.dataset.oldNisn : button.dataset.nisn;
        form.querySelector('[name="jenis_kelamin"]').value = useOldInput ? form.dataset.oldGender : button.dataset.gender;
        form.querySelector('[name="riwayat_alergi"]').value = useOldInput ? form.dataset.oldAllergies : button.dataset.allergies;
        setDialogQuery('edit', button.dataset.id);
        editDialog.showModal();
    };

    app.querySelector('[data-open-student-create]')?.addEventListener('click', openCreateDialog);
    app.querySelectorAll('[data-edit-student]').forEach((button) => {
        button.addEventListener('click', () => openEditDialog(button));
    });

    app.querySelectorAll('[data-close-student-dialog]').forEach((button) => {
        button.addEventListener('click', () => {
            const dialog = button.closest('dialog');
            dialog.close();
            setDialogQuery(dialog.dataset.kind, null);
        });
    });
    app.querySelectorAll('[data-student-dialog]').forEach((dialog) => {
        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) {
                dialog.close();
                setDialogQuery(dialog.dataset.kind, null);
            }
        });
    });
    app.querySelectorAll('[data-delete-student]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm('Hapus data dan akun login siswa ini? Penghapusan tidak bisa dibatalkan.')) {
                event.preventDefault();
            }
        });
    });

    const params = new URLSearchParams(window.location.search);
    if (params.has('create')) {
        createDialog.showModal();
    } else if (params.has('edit')) {
        const button = app.querySelector(`[data-edit-student][data-id="${CSS.escape(params.get('edit'))}"]`);
        if (button) {
            openEditDialog(button, true);
        }
    }
});

document.querySelectorAll('.siuks-student-page').forEach((app) => {
    const createDialog = app.querySelector('#admin-student-create-dialog');
    const editDialog = app.querySelector('#admin-student-edit-dialog');

    if (!createDialog || !editDialog) {
        return;
    }

    const setDialogQuery = (key, value) => {
        const url = new URL(window.location.href);
        if (value) {
            url.searchParams.set(key, value);
        } else {
            url.searchParams.delete(key);
        }
        window.history.replaceState({}, '', url);
    };

    const openEditDialog = (button, restoreOldInput = false) => {
        const form = editDialog.querySelector('[data-admin-student-form]');
        const useOldInput = restoreOldInput && app.dataset.validationErrors === 'true';
        form.action = button.dataset.url;
        form.querySelector('[name="name"]').value = useOldInput ? form.dataset.oldName : button.dataset.name;
        form.querySelector('[name="email"]').value = useOldInput ? form.dataset.oldEmail : button.dataset.email;
        form.querySelector('[name="nisn"]').value = useOldInput ? form.dataset.oldNisn : button.dataset.nisn;
        form.querySelector('[name="kelas"]').value = useOldInput ? form.dataset.oldClass : button.dataset.class;
        form.querySelector('[name="jenis_kelamin"]').value = useOldInput ? form.dataset.oldGender : button.dataset.gender;
        form.querySelector('[name="riwayat_alergi"]').value = useOldInput ? form.dataset.oldAllergies : button.dataset.allergies;
        setDialogQuery('edit', button.dataset.id);
        editDialog.showModal();
    };

    app.querySelector('[data-open-admin-student-create]')?.addEventListener('click', () => {
        setDialogQuery('create', '1');
        createDialog.showModal();
    });
    app.querySelectorAll('[data-edit-admin-student]').forEach((button) => {
        button.addEventListener('click', () => openEditDialog(button));
    });
    app.querySelectorAll('[data-close-admin-student-dialog]').forEach((button) => {
        button.addEventListener('click', () => {
            const dialog = button.closest('dialog');
            dialog.close();
            setDialogQuery(dialog.dataset.kind, null);
        });
    });
    app.querySelectorAll('[data-admin-student-dialog]').forEach((dialog) => {
        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) {
                dialog.close();
                setDialogQuery(dialog.dataset.kind, null);
            }
        });
    });
    app.querySelectorAll('[data-delete-admin-student]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm('Hapus data dan akun login siswa ini? Penghapusan tidak bisa dibatalkan.')) {
                event.preventDefault();
            }
        });
    });

    const params = new URLSearchParams(window.location.search);
    if (params.has('create')) {
        createDialog.showModal();
    } else if (params.has('edit')) {
        const button = app.querySelector(`[data-edit-admin-student][data-id="${CSS.escape(params.get('edit'))}"]`);
        if (button) {
            openEditDialog(button, true);
        }
    }
});

document.querySelectorAll('.siuks-wali-admin-page').forEach((app) => {
    const createDialog = app.querySelector('#admin-wali-create-dialog');
    const editDialog = app.querySelector('#admin-wali-edit-dialog');

    if (!createDialog || !editDialog) {
        return;
    }

    const setDialogQuery = (key, value) => {
        const url = new URL(window.location.href);
        if (value) {
            url.searchParams.set(key, value);
        } else {
            url.searchParams.delete(key);
        }
        window.history.replaceState({}, '', url);
    };

    const openEditDialog = (button, restoreOldInput = false) => {
        const form = editDialog.querySelector('[data-admin-wali-form]');
        const useOldInput = restoreOldInput && app.dataset.validationErrors === 'true';
        form.action = button.dataset.url;
        form.querySelector('[name="name"]').value = useOldInput ? form.dataset.oldName : button.dataset.name;
        form.querySelector('[name="email"]').value = useOldInput ? form.dataset.oldEmail : button.dataset.email;
        form.querySelector('[name="nip"]').value = useOldInput ? form.dataset.oldNip : button.dataset.nip;
        form.querySelector('[name="jurusan"]').value = useOldInput ? form.dataset.oldMajor : button.dataset.major;
        form.querySelector('[name="kelas_dampu"]').value = useOldInput ? form.dataset.oldClass : button.dataset.class;
        form.querySelector('[name="status"]').value = useOldInput ? form.dataset.oldStatus : button.dataset.status;
        setDialogQuery('edit', button.dataset.id);
        editDialog.showModal();
    };

    app.querySelector('[data-open-admin-wali-create]')?.addEventListener('click', () => {
        setDialogQuery('create', '1');
        createDialog.showModal();
    });
    app.querySelectorAll('[data-edit-admin-wali]').forEach((button) => {
        button.addEventListener('click', () => openEditDialog(button));
    });
    app.querySelectorAll('[data-close-admin-wali-dialog]').forEach((button) => {
        button.addEventListener('click', () => {
            const dialog = button.closest('dialog');
            dialog.close();
            setDialogQuery(dialog.dataset.kind, null);
        });
    });
    app.querySelectorAll('[data-admin-wali-dialog]').forEach((dialog) => {
        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) {
                dialog.close();
                setDialogQuery(dialog.dataset.kind, null);
            }
        });
    });
    app.querySelectorAll('[data-delete-admin-wali]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm('Hapus data dan akun login wali kelas ini? Penghapusan tidak bisa dibatalkan.')) {
                event.preventDefault();
            }
        });
    });

    const params = new URLSearchParams(window.location.search);
    if (params.has('create')) {
        createDialog.showModal();
    } else if (params.has('edit')) {
        const button = app.querySelector(`[data-edit-admin-wali][data-id="${CSS.escape(params.get('edit'))}"]`);
        if (button) {
            openEditDialog(button, true);
        }
    }
});

document.querySelectorAll('.siuks-pmr-admin-page').forEach((app) => {
    const createDialog = app.querySelector('#admin-pmr-create-dialog');
    const editDialog = app.querySelector('#admin-pmr-edit-dialog');

    if (!createDialog || !editDialog) {
        return;
    }

    const setDialogQuery = (key, value) => {
        const url = new URL(window.location.href);
        if (value) {
            url.searchParams.set(key, value);
        } else {
            url.searchParams.delete(key);
        }
        window.history.replaceState({}, '', url);
    };

    const openEditDialog = (button, restoreOldInput = false) => {
        const form = editDialog.querySelector('[data-admin-pmr-form]');
        const useOldInput = restoreOldInput && app.dataset.validationErrors === 'true';
        form.action = button.dataset.url;
        form.querySelector('[name="name"]').value = useOldInput ? form.dataset.oldName : button.dataset.name;
        form.querySelector('[name="email"]').value = useOldInput ? form.dataset.oldEmail : button.dataset.email;
        form.querySelector('[name="nis"]').value = useOldInput ? form.dataset.oldNis : button.dataset.nis;
        form.querySelector('[name="kelas"]').value = useOldInput ? form.dataset.oldClass : button.dataset.class;
        form.querySelector('[name="jenis_kelamin"]').value = useOldInput ? form.dataset.oldGender : button.dataset.gender;
        form.querySelector('[name="status"]').value = useOldInput ? form.dataset.oldStatus : button.dataset.status;
        setDialogQuery('edit', button.dataset.id);
        editDialog.showModal();
    };

    app.querySelector('[data-open-admin-pmr-create]')?.addEventListener('click', () => {
        setDialogQuery('create', '1');
        createDialog.showModal();
    });
    app.querySelectorAll('[data-edit-admin-pmr]').forEach((button) => {
        button.addEventListener('click', () => openEditDialog(button));
    });
    app.querySelectorAll('[data-close-admin-pmr-dialog]').forEach((button) => {
        button.addEventListener('click', () => {
            const dialog = button.closest('dialog');
            dialog.close();
            setDialogQuery(dialog.dataset.kind, null);
        });
    });
    app.querySelectorAll('[data-admin-pmr-dialog]').forEach((dialog) => {
        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) {
                dialog.close();
                setDialogQuery(dialog.dataset.kind, null);
            }
        });
    });
    app.querySelectorAll('[data-delete-admin-pmr]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm('Hapus data dan akun petugas PMR ini? Penghapusan ditolak jika petugas memiliki riwayat layanan.')) {
                event.preventDefault();
            }
        });
    });

    const params = new URLSearchParams(window.location.search);
    if (params.has('create')) {
        createDialog.showModal();
    } else if (params.has('edit')) {
        const button = app.querySelector(`[data-edit-admin-pmr][data-id="${CSS.escape(params.get('edit'))}"]`);
        if (button) {
            openEditDialog(button, true);
        }
    }
});

document.querySelectorAll('.siuks-medicine-admin-page').forEach((app) => {
    const dialog = app.querySelector('#admin-medicine-dialog');
    const form = dialog?.querySelector('[data-admin-medicine-form]');
    if (!dialog || !form) {
        return;
    }

    const title = form.querySelector('[data-admin-medicine-title]');
    const method = form.querySelector('[name="_method"]');
    const fields = {
        code: form.querySelector('[name="kode_obat"]'),
        name: form.querySelector('[name="nama_obat"]'),
        kind: form.querySelector('[name="jenis"]'),
        category: form.querySelector('[name="kategori"]'),
        stock: form.querySelector('[name="stok_tersedia"]'),
        unit: form.querySelector('[name="satuan"]'),
    };
    const setDialogQuery = (key, value) => {
        const url = new URL(window.location.href);
        if (value) {
            url.searchParams.set(key, value);
        } else {
            url.searchParams.delete('edit');
            url.searchParams.delete('create');
        }
        window.history.replaceState({}, '', url);
    };
    const openCreateDialog = (restoreOldInput = false) => {
        if (!restoreOldInput) {
            form.reset();
        }
        form.action = form.dataset.storeUrl;
        method.value = 'POST';
        title.textContent = 'Tambah obat';
        setDialogQuery('create', '1');
        dialog.showModal();
    };
    const openEditDialog = (button, restoreOldInput = false) => {
        const useOldInput = restoreOldInput && form.dataset.invalid === 'true';
        form.action = button.dataset.url;
        method.value = 'PUT';
        title.textContent = 'Edit obat';
        fields.code.value = useOldInput ? form.dataset.oldCode : button.dataset.code;
        fields.name.value = useOldInput ? form.dataset.oldName : button.dataset.name;
        fields.kind.value = useOldInput ? form.dataset.oldKind : button.dataset.kind;
        fields.category.value = useOldInput ? form.dataset.oldCategory : button.dataset.category;
        fields.stock.value = useOldInput ? form.dataset.oldStock : button.dataset.stock;
        fields.unit.value = useOldInput ? form.dataset.oldUnit : button.dataset.unit;
        setDialogQuery('edit', button.dataset.id);
        dialog.showModal();
    };

    app.querySelector('[data-open-admin-medicine-create]')?.addEventListener('click', openCreateDialog);
    app.querySelectorAll('[data-edit-admin-medicine]').forEach((button) => {
        button.addEventListener('click', () => openEditDialog(button));
    });
    app.querySelectorAll('[data-close-admin-medicine-dialog]').forEach((button) => {
        button.addEventListener('click', () => {
            dialog.close();
            setDialogQuery(null, null);
        });
    });
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) {
            dialog.close();
            setDialogQuery(null, null);
        }
    });
    app.querySelectorAll('[data-delete-admin-medicine]').forEach((deleteForm) => {
        deleteForm.addEventListener('submit', (event) => {
            if (!window.confirm('Hapus obat ini dari master? Obat yang sudah memiliki riwayat pemberian tidak dapat dihapus.')) {
                event.preventDefault();
            }
        });
    });

    const params = new URLSearchParams(window.location.search);
    if (params.has('create')) {
        openCreateDialog(true);
    } else if (params.has('edit')) {
        const button = app.querySelector(`[data-edit-admin-medicine][data-id="${CSS.escape(params.get('edit'))}"]`);
        if (button) {
            openEditDialog(button, true);
        }
    }
});

const medicineDialog = document.querySelector('#medicine-dialog');

if (medicineDialog) {
    const medicineForm = medicineDialog.querySelector('#medicine-form');
    const methodInput = medicineDialog.querySelector('#medicine-method');
    const idInput = medicineDialog.querySelector('#medicine-id');
    const dialogTitle = medicineDialog.querySelector('#medicine-dialog-title');
    const fields = {
        code: medicineDialog.querySelector('#medicine-code'),
        name: medicineDialog.querySelector('#medicine-name'),
        kind: medicineDialog.querySelector('#medicine-kind'),
        category: medicineDialog.querySelector('#medicine-category'),
        stock: medicineDialog.querySelector('#medicine-stock'),
        unit: medicineDialog.querySelector('#medicine-unit'),
    };

    const resetMedicineForm = () => {
        medicineForm.reset();
        medicineForm.action = medicineForm.dataset.storeUrl;
        methodInput.value = 'POST';
        idInput.value = '';
        dialogTitle.textContent = 'Tambah obat';
    };

    medicineForm.dataset.storeUrl = medicineForm.action;
    document.querySelector('[data-open-medicine-dialog]')?.addEventListener('click', () => {
        resetMedicineForm();
        medicineDialog.showModal();
    });

    document.querySelectorAll('[data-edit-medicine]').forEach((button) => {
        button.addEventListener('click', () => {
            resetMedicineForm();
            medicineForm.action = `/pmr/obat/${button.dataset.id}`;
            methodInput.value = 'PUT';
            idInput.value = button.dataset.id;
            dialogTitle.textContent = 'Edit obat';
            fields.code.value = button.dataset.code;
            fields.name.value = button.dataset.name;
            fields.kind.value = button.dataset.kind;
            fields.category.value = button.dataset.category;
            fields.stock.value = button.dataset.stock;
            fields.unit.value = button.dataset.unit;
            medicineDialog.showModal();
        });
    });

    medicineDialog.querySelectorAll('[data-close-medicine-dialog]').forEach((button) => {
        button.addEventListener('click', () => medicineDialog.close());
    });

    medicineDialog.addEventListener('click', (event) => {
        if (event.target === medicineDialog) {
            medicineDialog.close();
        }
    });

    document.querySelectorAll('[data-delete-medicine]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm('Hapus obat ini dari persediaan?')) {
                event.preventDefault();
            }
        });
    });

    if (medicineForm.dataset.invalid === 'true') {
        dialogTitle.textContent = idInput.value ? 'Edit obat' : 'Tambah obat';
        medicineDialog.showModal();
    }
}

document.querySelectorAll('form:not([data-sos-form]):not([data-no-submit-feedback])').forEach((form) => {
    if (form.method.toUpperCase() === 'GET') {
        return;
    }

    form.addEventListener('submit', (event) => {
        if (event.defaultPrevented) {
            return;
        }

        if (form.dataset.submitting === 'true') {
            event.preventDefault();
            return;
        }

        const button = event.submitter || form.querySelector('button:not([type]), button[type="submit"], input[type="submit"]');
        if (!button || button.disabled) {
            return;
        }

        form.dataset.submitting = 'true';
        button.disabled = true;
        button.setAttribute('aria-busy', 'true');

        if (button instanceof HTMLInputElement) {
            button.value = 'Memproses...';
        } else {
            button.textContent = 'Memproses...';
        }
    });
});
