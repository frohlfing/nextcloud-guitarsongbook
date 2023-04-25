"use strict";

// Source
// Discussion: https://github.com/CoderLine/alphaTab/discussions/1123
// Demo Page: https://demo.alphatab.net/alphatex.html#exporter
// TypoScript: https://github.com/izszzz/tunescore/blob/c10c644f13a8e5e1013f3322321a1e5b9f58d95e/src/helpers/AlphaTexExporter.ts

// Build 1

window.AlphaTexExporter = /** @class */ (function () {
    function AlphaTexExporter() {
        this._builder = "";
    }
    AlphaTexExporter.prototype.Export = function (score) {
        this.Score(score);
    };
    AlphaTexExporter.prototype.Score = function (score) {
        var _this = this;
        this.ScoreMetaData(score);
        console.log(score);
        score.tracks.forEach(function (track) {
            _this.TrackMetaData(track);
            _this.Bars(track);
        });
    };
    AlphaTexExporter.prototype.ToTex = function () {
        return this._builder;
    };
    AlphaTexExporter.prototype.ScoreMetaData = function (score) {
        this.StringMetaData("title", score.title);
        this.StringMetaData("subtitle", score.subTitle);
        this.StringMetaData("artist", score.artist);
        this.StringMetaData("album", score.album);
        this.StringMetaData("words", score.words);
        this.StringMetaData("music", score.music);
        this.StringMetaData("copyright", score === null || score === void 0 ? void 0 : score.copyright);
        this._builder += "\\tempo ";
        this._builder += score.tempo;
        this._builder += "" + "\r\n";
        this._builder += ".";
        this._builder += "" + "\r\n";
    };
    AlphaTexExporter.prototype.TrackMetaData = function (track) {
        this._builder += "" + "\r\n";
        // eslint-disable-next-line no-useless-escape
        this._builder += "\\track \"".concat(track.name, "\" \"").concat(track.shortName, "\"");
        this._builder += "" + "\r\n";
    };
    AlphaTexExporter.prototype.StaveMetaData = function (stave) {
        var _this = this;
        this._builder += "\\staff ";
        this._builder += "" + "\r\n";
        if (stave.capo > 0) {
            this._builder += "\\capo ".concat(stave.capo);
        }
        this._builder += "\\tuning";
        stave.stringTuning.tunings.forEach(function (tuning) {
            _this._builder += " ";
            _this._builder += alphaTab.model.Tuning.getTextForTuning(tuning, true);
        });
        this._builder += "" + "\r\n";
        this._builder += "\\instrument ".concat(stave.track.playbackInfo.program);
        this._builder += "" + "\r\n";
        this._builder += "" + "\r\n";
    };
    AlphaTexExporter.prototype.StringMetaData = function (key, value) {
        if (value && value.trim() === "") {
            this._builder += "\\";
            this._builder += key;
            this._builder += ' "';
            this._builder += value.replace('"', '\\"');
            this._builder += '"';
            this._builder += "" + "\r\n";
        }
    };
    AlphaTexExporter.prototype.Bars = function (track) {
        var _this = this;
        track.staves.forEach(function (stave) {
            _this.StaveMetaData(stave);
            stave.bars.forEach(function (bar, i) {
                if (i > 0) {
                    _this._builder += " |";
                    _this._builder += "" + "\r\n";
                }
                _this.Bar(bar);
            });
        });
    };
    AlphaTexExporter.prototype.Bar = function (bar) {
        this.BarMeta(bar);
        if (bar.voices[0])
            this.Voice(bar.voices[0]);
    };
    AlphaTexExporter.prototype.Voice = function (voice) {
        var _this = this;
        voice.beats.forEach(function (beat) { return _this.Beat(beat); });
    };
    AlphaTexExporter.prototype.Beat = function (beat) {
        var _this = this;
        if (beat.isRest) {
            this._builder += "r";
        }
        else {
            if (beat.notes.length > 1) {
                this._builder += "(";
            }
            beat.notes.forEach(function (note) { return _this.Note(note); });
            if (beat.notes.length > 1) {
                this._builder += ")";
            }
        }
        this._builder += ".";
        this._builder += beat.duration;
        this._builder += " ";
        this.BeatEffects(beat);
    };
    AlphaTexExporter.prototype.Note = function (note) {
        if (note.isDead) {
            this._builder += "x";
        }
        else if (note.isTieDestination) {
            this._builder += "-";
        }
        else {
            this._builder += note.fret;
        }
        this._builder += ".";
        if (note.beat.voice.bar.staff.track.staves[0])
            this._builder +=
                note.beat.voice.bar.staff.track.staves[0].stringTuning.tunings.length -
                    note.string +
                    1;
        this._builder += " ";
        this.NoteEffects(note);
    };
    AlphaTexExporter.prototype.NoteEffects = function (note) {
        var _a, _b;
        var hasEffectOpen = false;
        if (note.hasBend) {
            hasEffectOpen = this.EffectOpen(hasEffectOpen);
            this._builder += "b (";
            for (var i = 0; i < note.bendPoints.length; i++) {
                console.log(note);
                this._builder += (_a = note.bendPoints[i]) === null || _a === void 0 ? void 0 : _a.offset;
                this._builder += " ";
                this._builder += (_b = note.bendPoints[i]) === null || _b === void 0 ? void 0 : _b.value;
                this._builder += " ";
            }
            this._builder += ")";
        }
        switch (note.harmonicType) {
            case alphaTab.model.HarmonicType.Natural:
                hasEffectOpen = this.EffectOpen(hasEffectOpen);
                this._builder += "nh ";
                break;
            case alphaTab.model.HarmonicType.Artificial:
                hasEffectOpen = this.EffectOpen(hasEffectOpen);
                this._builder += "ah ";
                break;
            case alphaTab.model.HarmonicType.Tap:
                hasEffectOpen = this.EffectOpen(hasEffectOpen);
                this._builder += "th ";
                break;
            case alphaTab.model.HarmonicType.Pinch:
                hasEffectOpen = this.EffectOpen(hasEffectOpen);
                this._builder += "ph ";
                break;
            case alphaTab.model.HarmonicType.Semi:
                hasEffectOpen = this.EffectOpen(hasEffectOpen);
                this._builder += "sh ";
                break;
        }
        if (note.isTrill) {
            hasEffectOpen = this.EffectOpen(hasEffectOpen);
            this._builder += "tr ";
            this._builder += note.trillFret;
            this._builder += " ";
            switch (note.trillSpeed) {
                case alphaTab.model.Duration.Sixteenth:
                    this._builder += "16 ";
                    break;
                case alphaTab.model.Duration.ThirtySecond:
                    this._builder += "32 ";
                    break;
                case alphaTab.model.Duration.SixtyFourth:
                    this._builder += "64 ";
                    break;
            }
        }
        if (note.vibrato != alphaTab.model.VibratoType.None) {
            hasEffectOpen = this.EffectOpen(hasEffectOpen);
            this._builder += "v ";
        }
        switch (note.slideOutType) {
            case alphaTab.model.SlideOutType.Legato:
                hasEffectOpen = this.EffectOpen(hasEffectOpen);
                this._builder += "sl ";
                break;
            case alphaTab.model.SlideOutType.Shift:
                hasEffectOpen = this.EffectOpen(hasEffectOpen);
                this._builder += "ss ";
                break;
            case alphaTab.model.SlideOutType.PickSlideDown:
                hasEffectOpen = this.EffectOpen(hasEffectOpen);
                this._builder += "psd ";
                break;
            case alphaTab.model.SlideOutType.PickSlideUp:
                hasEffectOpen = this.EffectOpen(hasEffectOpen);
                this._builder += "psu ";
                break;
            case alphaTab.model.SlideOutType.OutUp:
                hasEffectOpen = this.EffectOpen(hasEffectOpen);
                this._builder += "sou ";
                break;
            case alphaTab.model.SlideOutType.OutDown:
                hasEffectOpen = this.EffectOpen(hasEffectOpen);
                this._builder += "sod ";
                break;
        }
        switch (note.slideInType) {
            case alphaTab.model.SlideInType.IntoFromBelow:
                hasEffectOpen = this.EffectOpen(hasEffectOpen);
                this._builder += "sib ";
                break;
            case alphaTab.model.SlideInType.IntoFromAbove:
                hasEffectOpen = this.EffectOpen(hasEffectOpen);
                this._builder += "sia ";
                break;
        }
        if (note.isHammerPullOrigin) {
            hasEffectOpen = this.EffectOpen(hasEffectOpen);
            this._builder += "h ";
        }
        if (note.isGhost) {
            hasEffectOpen = this.EffectOpen(hasEffectOpen);
            this._builder += "g ";
        }
        if (note.accentuated == alphaTab.model.AccentuationType.Normal) {
            hasEffectOpen = this.EffectOpen(hasEffectOpen);
            this._builder += "ac ";
        }
        else if (note.accentuated == alphaTab.model.AccentuationType.Heavy) {
            hasEffectOpen = this.EffectOpen(hasEffectOpen);
            this._builder += "hac ";
        }
        if (note.isPalmMute) {
            hasEffectOpen = this.EffectOpen(hasEffectOpen);
            this._builder += "pm ";
        }
        if (note.isStaccato) {
            hasEffectOpen = this.EffectOpen(hasEffectOpen);
            this._builder += "st ";
        }
        if (note.isLetRing) {
            hasEffectOpen = this.EffectOpen(hasEffectOpen);
            this._builder += "lr ";
        }
        switch (note.leftHandFinger) {
            case alphaTab.model.Fingers.Thumb:
                hasEffectOpen = this.EffectOpen(hasEffectOpen);
                this._builder += "1 ";
                break;
            case alphaTab.model.Fingers.IndexFinger:
                hasEffectOpen = this.EffectOpen(hasEffectOpen);
                this._builder += "2 ";
                break;
            case alphaTab.model.Fingers.MiddleFinger:
                hasEffectOpen = this.EffectOpen(hasEffectOpen);
                this._builder += "3 ";
                break;
            case alphaTab.model.Fingers.AnnularFinger:
                hasEffectOpen = this.EffectOpen(hasEffectOpen);
                this._builder += "4 ";
                break;
            case alphaTab.model.Fingers.LittleFinger:
                hasEffectOpen = this.EffectOpen(hasEffectOpen);
                this._builder += "5 ";
                break;
        }
        switch (note.rightHandFinger) {
            case alphaTab.model.Fingers.Thumb:
                hasEffectOpen = this.EffectOpen(hasEffectOpen);
                this._builder += "1 ";
                break;
            case alphaTab.model.Fingers.IndexFinger:
                hasEffectOpen = this.EffectOpen(hasEffectOpen);
                this._builder += "2 ";
                break;
            case alphaTab.model.Fingers.MiddleFinger:
                hasEffectOpen = this.EffectOpen(hasEffectOpen);
                this._builder += "3 ";
                break;
            case alphaTab.model.Fingers.AnnularFinger:
                hasEffectOpen = this.EffectOpen(hasEffectOpen);
                this._builder += "4 ";
                break;
            case alphaTab.model.Fingers.LittleFinger:
                hasEffectOpen = this.EffectOpen(hasEffectOpen);
                this._builder += "5 ";
                break;
        }
        this.EffectClose(hasEffectOpen);
    };
    AlphaTexExporter.prototype.EffectOpen = function (hasBeatEffectOpen) {
        if (!hasBeatEffectOpen) {
            this._builder += "{";
        }
        return true;
    };
    AlphaTexExporter.prototype.EffectClose = function (hasBeatEffectOpen) {
        if (hasBeatEffectOpen) {
            this._builder += "}";
        }
    };
    AlphaTexExporter.prototype.BeatEffects = function (beat) {
        var _a, _b;
        var hasEffectOpen = false;
        if (beat.fadeIn) {
            hasEffectOpen = this.EffectOpen(hasEffectOpen);
            this._builder += "f ";
        }
        switch (beat.graceType) {
            case alphaTab.model.GraceType.OnBeat:
                this._builder += "gr ob ";
                break;
            case alphaTab.model.GraceType.BeforeBeat:
                this._builder += "gr ";
                break;
        }
        if (beat.vibrato != alphaTab.model.VibratoType.None) {
            hasEffectOpen = this.EffectOpen(hasEffectOpen);
            this._builder += "v ";
        }
        if (beat.slap) {
            hasEffectOpen = this.EffectOpen(hasEffectOpen);
            this._builder += "s ";
        }
        if (beat.pop) {
            hasEffectOpen = this.EffectOpen(hasEffectOpen);
            this._builder += "p ";
        }
        if (beat.dots == 2) {
            hasEffectOpen = this.EffectOpen(hasEffectOpen);
            this._builder += "dd ";
        }
        else if (beat.dots == 1) {
            hasEffectOpen = this.EffectOpen(hasEffectOpen);
            this._builder += "d ";
        }
        //TODO:
        //     if (beat.PickStroke == Model.PickStrokeType.Up) {
        //       hasEffectOpen = this.EffectOpen(hasEffectOpen)
        //       this._builder += "su "
        //     } else if (beat.PickStroke == Model.PickStrokeType.Down) {
        //       hasEffectOpen = this.EffectOpen(hasEffectOpen)
        //       this._builder += "sd "
        //     }
        if (beat.hasTuplet) {
            var tupletValue = 0;
            if (beat.tupletDenominator == 3 && beat.tupletNumerator == 2) {
                tupletValue = 3;
            }
            else if (beat.tupletDenominator == 5 && beat.tupletNumerator == 4) {
                tupletValue = 5;
            }
            else if (beat.tupletDenominator == 6 && beat.tupletNumerator == 4) {
                tupletValue = 6;
            }
            else if (beat.tupletDenominator == 7 && beat.tupletNumerator == 4) {
                tupletValue = 7;
            }
            else if (beat.tupletDenominator == 9 && beat.tupletNumerator == 8) {
                tupletValue = 9;
            }
            else if (beat.tupletDenominator == 10 && beat.tupletNumerator == 8) {
                tupletValue = 10;
            }
            else if (beat.tupletDenominator == 11 && beat.tupletNumerator == 8) {
                tupletValue = 11;
            }
            else if (beat.tupletDenominator == 12 && beat.tupletNumerator == 8) {
                tupletValue = 12;
            }
            if (tupletValue != 0) {
                hasEffectOpen = this.EffectOpen(hasEffectOpen);
                this._builder += "tu ";
                this._builder += tupletValue;
                this._builder += " ";
            }
        }
        if (beat.hasWhammyBar) {
            hasEffectOpen = this.EffectOpen(hasEffectOpen);
            this._builder += "tbe (";
            for (var i = 0; i < beat.whammyBarPoints.length; i++) {
                this._builder += (_a = beat.whammyBarPoints[i]) === null || _a === void 0 ? void 0 : _a.offset;
                this._builder += " ";
                this._builder += (_b = beat.whammyBarPoints[i]) === null || _b === void 0 ? void 0 : _b.value;
                this._builder += " ";
            }
            this._builder += ")";
        }
        if (beat.isTremolo) {
            hasEffectOpen = this.EffectOpen(hasEffectOpen);
            this._builder += "tp ";
            //TODO:
            //       if (beat.TremoloSpeed == AlphaTab.Model.Duration.Eighth) {
            //         this._builder += "8 "
            //       } else if (beat.TremoloSpeed == AlphaTab.Model.Duration.Sixteenth) {
            //         this._builder += "16 "
            //       } else if (beat.TremoloSpeed == AlphaTab.Model.Duration.ThirtySecond) {
            //         this._builder += "32 "
            //       } else {
            //         this._builder += "8 "
            //       }
        }
        this.EffectClose(hasEffectOpen);
    };
    AlphaTexExporter.prototype.BarMeta = function (bar) {
        var masterBar = bar.masterBar;
        if (masterBar.index > 0) {
            var previousMasterBar = masterBar.previousMasterBar;
            var previousBar = bar.previousBar;
            if ((previousMasterBar === null || previousMasterBar === void 0 ? void 0 : previousMasterBar.timeSignatureDenominator) !=
                masterBar.timeSignatureDenominator ||
                (previousMasterBar === null || previousMasterBar === void 0 ? void 0 : previousMasterBar.timeSignatureNumerator) !=
                    masterBar.timeSignatureNumerator) {
                this._builder += "\\ts ";
                this._builder += masterBar.timeSignatureNumerator;
                this._builder += " ";
                this._builder += masterBar.timeSignatureDenominator;
                this._builder += "" + "\r\n";
            }
            if ((previousMasterBar === null || previousMasterBar === void 0 ? void 0 : previousMasterBar.keySignature) != masterBar.keySignature) {
                this._builder += "\\ks ";
                switch (masterBar.keySignature) {
                    case -7:
                        this._builder += "cb";
                        break;
                    case -6:
                        this._builder += "gb";
                        break;
                    case -5:
                        this._builder += "db";
                        break;
                    case -4:
                        this._builder += "ab";
                        break;
                    case -3:
                        this._builder += "eb";
                        break;
                    case -2:
                        this._builder += "bb";
                        break;
                    case -1:
                        this._builder += "f";
                        break;
                    case 0:
                        this._builder += "c";
                        break;
                    case 1:
                        this._builder += "g";
                        break;
                    case 2:
                        this._builder += "d";
                        break;
                    case 3:
                        this._builder += "a";
                        break;
                    case 4:
                        this._builder += "e";
                        break;
                    case 5:
                        this._builder += "b";
                        break;
                    case 6:
                        this._builder += "f#";
                        break;
                    case 7:
                        this._builder += "c#";
                        break;
                }
                this._builder += "" + "\r\n";
            }
            if (bar.clef != (previousBar === null || previousBar === void 0 ? void 0 : previousBar.clef)) {
                this._builder += "\\clef ";
                switch (bar.clef) {
                    case alphaTab.model.Clef.Neutral:
                        this._builder += "n";
                        break;
                    case alphaTab.model.Clef.C3:
                        this._builder += "c3";
                        break;
                    case alphaTab.model.Clef.C4:
                        this._builder += "c4";
                        break;
                    case alphaTab.model.Clef.F4:
                        this._builder += "f4";
                        break;
                    case alphaTab.model.Clef.G2:
                        this._builder += "g2";
                        break;
                }
                this._builder += "" + "\r\n";
            }
            if (masterBar.tempoAutomation != null) {
                this._builder += "\\tempo ";
                this._builder += masterBar.tempoAutomation.value;
                this._builder += "" + "\r\n";
            }
        }
        if (masterBar.isRepeatStart) {
            this._builder += "\\ro ";
            this._builder += "" + "\r\n";
        }
        if (masterBar.isRepeatEnd) {
            this._builder += "\\rc ";
            this._builder += masterBar.repeatCount + 1;
            this._builder += "" + "\r\n";
        }
    };
    return AlphaTexExporter;
}());
