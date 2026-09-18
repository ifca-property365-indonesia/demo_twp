function ShowPCx(e) {
    if (
        "undefined" == typeof document.getElementById ||
        "undefined" == typeof document.styleSheets
    )
        return !1;
    if (null == e) return !1;
    e._contextwrapper = this.createContextWrapper(e);
    var t = e._contextwrapper,
        a = this.convertPasswordFieldHTML(e),
        n = Math.round(1e3 * Math.random()),
        i =
            '<label for="showpasscheckbox-' +
            n +
            '" class="show-password" title="Show the password as plain text (not advisable in a public place)" style="display:block;position:static;font-weight: 100 !important;padding-left: 15px;padding-top:10px;">';
    (i +=
        '<input type="checkbox" id="showpasscheckbox-' +
        n +
        '" title="Show the password as plain text (not advisable in a public place)">'),
        (i +=
            '<span style="display:inline-block; color:#000000;padding-left: 5px;">Show Password</span>'),
        (i += "</label>"),
        (t.innerHTML += a + i);
    var a = t.lastChild.previousSibling,
        r = t.lastChild.firstChild;
    (e = t.firstChild),
        (e._plainfield = a),
        (a._passwordfield = e),
        (r._passwordfield = e),
        (r._plainfield = a),
        (e._contextwrapper = t);
    var l = this;
    this.addListener(e, "change", function (e) {
        var t = l.getTarget(e);
        t._plainfield.value = t.value;
    }),
        this.addListener(a, "change", function (e) {
            var t = l.getTarget(e);
            t._passwordfield.value = t.value;
        }),
        this.addListener(r, "click", function (e) {
            var t = l.getTarget(e),
                a = t.checked ? t._plainfield : t._passwordfield,
                n = t.checked ? t._passwordfield : t._plainfield;
            (a.value = n.value),
                (a.style.display = "block"),
                (n.style.display = "none");
        });
    var s = this.getParentForm(e);
    return (
        s &&
            ((s._plainfield = a),
            (s._passwordfield = e),
            this.addListener(s, "submit", function (e) {
                var t = l.getTarget(e);
                "block" == t._plainfield.style.display &&
                    (t._passwordfield.value = t._plainfield.value);
            })),
        !0
    );
}
ShowPCx.prototype = {
    createContextWrapper: function (e) {
        var t = document.createElement("span");
        return (
            (t.style.position = "relative"),
            e.parentNode.insertBefore(t, e),
            t.appendChild(e),
            t
        );
    },
    getParentForm: function (e) {
        for (; e && !/form/i.test(e.nodeName); ) e = e.parentNode;
        return /form/i.test(e.nodeName) ? e : null;
    },
    convertPasswordFieldHTML: function (e) {
        for (var t = "<input", a = e.attributes, n = 0; n < a.length; n++)
            a[n].specified &&
                !/^(_|type|name)/.test(a[n].name) &&
                (t += " " + a[n].name + '="' + a[n].value + '"');
        return (t += ' type="text" style="display:none" autocomplete="off">');
    },
    addListener: function (e, t, a) {
        return "undefined" != typeof document.addEventListener
            ? e.addEventListener(t, a, !1)
            : "undefined" != typeof document.attachEvent
            ? e.attachEvent("on" + t, a)
            : void 0;
    },
    getTarget: function (e) {
        return e ? (e.target ? e.target : e.srcElement) : null;
    },
};
