/*
 * FS-07 — Sicherer Ausdrucks-Evaluator (Ersatz für `new Function` in der Formular-Vorschau).
 *
 * Kein eval / new Function: Tokenizer → Recursive-Descent-Parser → direkte Auswertung über eine
 * Whitelist (Zahlen, String-Literale, Variablen, Funktionen add/sub/mul/div/round/min/max/toNum,
 * Operatoren + - * / , Vergleiche > < >= <= == != , Logik && || !). Nur Lese-Zugriff auf `values`;
 * unbekannte Bezeichner sind KEINE globalen Objekte (kein Zugriff auf window/document/etc.).
 *
 * Server bleibt die Autorität (product.formula.evaluate / FormulaEvaluationService); dieser Client-
 * Evaluator dient nur der Live-Vorschau im Builder.
 */
(function (root) {
    'use strict';

    var FUNCS = ['add', 'sub', 'mul', 'div', 'round', 'min', 'max', 'toNum'];

    function toNum(v) {
        var n = parseFloat(v);
        return isNaN(n) ? 0 : n;
    }

    var DEFAULT_FNS = {
        add: function () { return [].reduce.call(arguments, function (a, b) { return toNum(a) + toNum(b); }, 0); },
        sub: function (a, b) { return toNum(a) - toNum(b); },
        mul: function () { return [].reduce.call(arguments, function (a, b) { return toNum(a) * toNum(b); }, 1); },
        div: function (a, b) { return toNum(b) === 0 ? 0 : toNum(a) / toNum(b); },
        round: function (a, d) { var p = Math.pow(10, toNum(d)); return Math.round(toNum(a) * p) / p; },
        min: function () { return Math.min.apply(Math, [].map.call(arguments, toNum)); },
        max: function () { return Math.max.apply(Math, [].map.call(arguments, toNum)); },
        toNum: toNum
    };

    // --- Tokenizer -------------------------------------------------------
    function tokenize(src) {
        var tokens = [], i = 0, n = src.length;
        var two = ['>=', '<=', '==', '!=', '&&', '||'];
        while (i < n) {
            var c = src[i];
            if (c === ' ' || c === '\t' || c === '\n' || c === '\r') { i++; continue; }
            // Zahl
            if ((c >= '0' && c <= '9') || (c === '.' && src[i + 1] >= '0' && src[i + 1] <= '9')) {
                var num = '';
                while (i < n && ((src[i] >= '0' && src[i] <= '9') || src[i] === '.')) { num += src[i++]; }
                tokens.push({ t: 'num', v: parseFloat(num) });
                continue;
            }
            // String-Literal
            if (c === '"' || c === "'") {
                var q = c, str = ''; i++;
                while (i < n && src[i] !== q) {
                    if (src[i] === '\\' && i + 1 < n) { str += src[i + 1]; i += 2; } else { str += src[i++]; }
                }
                i++; // schließendes Quote
                tokens.push({ t: 'str', v: str });
                continue;
            }
            // Bezeichner
            if (/[a-zA-Z_]/.test(c)) {
                var id = '';
                while (i < n && /[a-zA-Z0-9_]/.test(src[i])) { id += src[i++]; }
                tokens.push({ t: 'id', v: id });
                continue;
            }
            // Zwei-Zeichen-Operator
            var pair = src.substr(i, 2);
            if (two.indexOf(pair) !== -1) { tokens.push({ t: 'op', v: pair }); i += 2; continue; }
            // Ein-Zeichen-Operator
            if ('+-*/()<>!,'.indexOf(c) !== -1) { tokens.push({ t: 'op', v: c }); i++; continue; }
            throw new Error('Unerlaubtes Zeichen: ' + c);
        }
        tokens.push({ t: 'end', v: null });
        return tokens;
    }

    // --- Parser / Evaluator (Recursive Descent) --------------------------
    function Parser(tokens, values, fns) {
        this.toks = tokens; this.pos = 0; this.values = values || {}; this.fns = fns || DEFAULT_FNS;
    }
    Parser.prototype.peek = function () { return this.toks[this.pos]; };
    Parser.prototype.next = function () { return this.toks[this.pos++]; };
    Parser.prototype.eat = function (v) {
        var tk = this.toks[this.pos];
        if (tk.v !== v) { throw new Error('Erwartet "' + v + '", gefunden "' + tk.v + '"'); }
        this.pos++;
    };
    Parser.prototype.isOp = function (v) { var tk = this.peek(); return tk.t === 'op' && tk.v === v; };

    Parser.prototype.parse = function () {
        var r = this.parseOr();
        if (this.peek().t !== 'end') { throw new Error('Unerwartetes Token: ' + this.peek().v); }
        return r;
    };
    Parser.prototype.parseOr = function () {
        var l = this.parseAnd();
        while (this.isOp('||')) { this.next(); var r = this.parseAnd(); l = (l || r); }
        return l;
    };
    Parser.prototype.parseAnd = function () {
        var l = this.parseEq();
        while (this.isOp('&&')) { this.next(); var r = this.parseEq(); l = (l && r); }
        return l;
    };
    Parser.prototype.parseEq = function () {
        var l = this.parseCmp();
        while (this.isOp('==') || this.isOp('!=')) {
            var op = this.next().v, r = this.parseCmp();
            l = op === '==' ? (l == r) : (l != r); // eslint-disable-line eqeqeq
        }
        return l;
    };
    Parser.prototype.parseCmp = function () {
        var l = this.parseAdd();
        while (this.isOp('>') || this.isOp('<') || this.isOp('>=') || this.isOp('<=')) {
            var op = this.next().v, r = this.parseAdd();
            l = op === '>' ? (l > r) : op === '<' ? (l < r) : op === '>=' ? (l >= r) : (l <= r);
        }
        return l;
    };
    Parser.prototype.parseAdd = function () {
        var l = this.parseMul();
        while (this.isOp('+') || this.isOp('-')) {
            var op = this.next().v, r = this.parseMul();
            l = op === '+' ? (this.num(l) + this.num(r)) : (this.num(l) - this.num(r));
        }
        return l;
    };
    Parser.prototype.parseMul = function () {
        var l = this.parseUnary();
        while (this.isOp('*') || this.isOp('/')) {
            var op = this.next().v, r = this.parseUnary();
            l = op === '*' ? (this.num(l) * this.num(r)) : (this.num(r) === 0 ? 0 : this.num(l) / this.num(r));
        }
        return l;
    };
    Parser.prototype.parseUnary = function () {
        if (this.isOp('-')) { this.next(); return -this.num(this.parseUnary()); }
        if (this.isOp('!')) { this.next(); return !this.parseUnary(); }
        return this.parsePrimary();
    };
    Parser.prototype.parsePrimary = function () {
        var tk = this.peek();
        if (tk.t === 'num') { this.next(); return tk.v; }
        if (tk.t === 'str') { this.next(); return tk.v; }
        if (this.isOp('(')) { this.next(); var e = this.parseOr(); this.eat(')'); return e; }
        if (tk.t === 'id') {
            this.next();
            if (this.isOp('(')) { // Funktionsaufruf
                this.next();
                var args = [];
                if (!this.isOp(')')) {
                    args.push(this.parseOr());
                    while (this.isOp(',')) { this.next(); args.push(this.parseOr()); }
                }
                this.eat(')');
                var fn = this.fns[tk.v];
                if (typeof fn !== 'function' || FUNCS.indexOf(tk.v) === -1) {
                    throw new Error('Unbekannte Funktion: ' + tk.v);
                }
                return fn.apply(null, args);
            }
            // Variable (nur aus values; KEIN globaler Zugriff)
            return Object.prototype.hasOwnProperty.call(this.values, tk.v) ? this.values[tk.v] : this.missing;
        }
        throw new Error('Unerwartetes Token: ' + tk.v);
    };
    Parser.prototype.num = function (v) { return typeof v === 'number' ? v : toNum(v); };

    // --- Öffentliche API -------------------------------------------------
    function evalArithmetic(formula, values, fns) {
        try {
            var p = new Parser(tokenize(String(formula)), values, fns || DEFAULT_FNS);
            p.missing = 0; // fehlende Operanden = 0 (wie bisher)
            var r = p.parse();
            return typeof r === 'boolean' ? (r ? 1 : 0) : r;
        } catch (e) {
            if (typeof console !== 'undefined') { console.warn('Formel-Fehler:', formula, e.message); }
            return 'Fehler';
        }
    }

    function evalCondition(condition, values) {
        if (condition === null || condition === undefined || String(condition).trim() === '') { return true; }
        try {
            var p = new Parser(tokenize(String(condition)), values, DEFAULT_FNS);
            p.missing = undefined; // fehlende Variable = undefined → Vergleich i.d.R. false
            return !!p.parse();
        } catch (e) {
            return false; // Fehler ⇒ ausblenden (wie bisheriges catch→return)
        }
    }

    var api = { evalArithmetic: evalArithmetic, evalCondition: evalCondition, DEFAULT_FNS: DEFAULT_FNS };
    if (typeof module !== 'undefined' && module.exports) { module.exports = api; }
    root.FormSafeEval = api;
})(typeof globalThis !== 'undefined' ? globalThis : this);
