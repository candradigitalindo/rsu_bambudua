var toolbarOptions = [
    ["bold", "italic", "underline", "strike"], // toggled buttons
    ["blockquote", "code-block"],

    [{ header: 1 }, { header: 2 }], // custom button values
    [{ list: "ordered" }, { list: "bullet" }],
    [{ script: "sub" }, { script: "super" }], // superscript/subscript
    [{ indent: "-1" }, { indent: "+1" }], // outdent/indent
    [{ direction: "rtl" }], // text direction

    [{ size: ["small", false, "large", "huge"] }], // custom dropdown
    [{ header: [1, 2, 3, 4, 5, 6, false] }],

    [{ color: [] }, { background: [] }], // dropdown with defaults from theme
    [{ font: [] }],
    [{ align: [] }],

    ["clean"], // remove formatting button
];

// Inisialisasi untuk #fullEditor
var quill;
if (document.querySelector("#fullEditor")) {
    quill = new Quill("#fullEditor", {
        theme: "snow",
        modules: {
            toolbar: toolbarOptions,
        },
    });
}

// Inisialisasi untuk #fullEditor2 (jika ada)
var quill2;
if (document.querySelector("#fullEditor2")) {
    quill2 = new Quill("#fullEditor2", {
        theme: "snow",
        modules: {
            toolbar: toolbarOptions,
        },
    });
}
// Inisialisasi untuk editor lain, misal #catatanEditor dan #diagnosisEditor
var quillCatatan;
if (document.querySelector("#catatanEditor")) {
    quillCatatan = new Quill("#catatanEditor", {
        theme: "snow",
        modules: {
            toolbar: toolbarOptions,
        },
    });
}

var quillDiagnosis;
if (document.querySelector("#diagnosisEditor")) {
    quillDiagnosis = new Quill("#diagnosisEditor", {
        theme: "snow",
        modules: {
            toolbar: toolbarOptions,
        },
    });
}
