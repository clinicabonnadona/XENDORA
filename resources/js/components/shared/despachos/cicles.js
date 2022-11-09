/* for (var x = 0; x < arrayAllMoths.length; x++) {
                                var match = false;

                                for (
                                    var y = 0;
                                    y < this.despachosAc.length;
                                    y++
                                ) {
                                    //console.log(this.despachosAc.length);
                                    for (
                                        var z = 0;
                                        z < this.despachosAc[y].length;
                                        z++
                                    ) {
                                        console.log(
                                            this.despachosAc[y][z].patient +
                                                " -- " +
                                                this.despachosAc[y][z].month +
                                                " -- " +
                                                this.despachosAc[y][z].year +
                                                " -- " +
                                                this.despachosAc[y][z].quantity
                                        );
                                        // Hast acá todo Ok
                                        if (
                                            arrayAllMoths[x] ===
                                            monthName[
                                                this.despachosAc[y][z].month
                                            ] +
                                                " " +
                                                this.despachosAc[y][z].year
                                        ) {
                                            match = true;
                                            allDespachosAc[x] = {
                                                [`${
                                                    monthName[
                                                        this.despachosAc[y][z]
                                                            .month
                                                    ]
                                                }`]: parseInt(
                                                    this.despachosAc[y][z]
                                                        .quantity
                                                ),
                                                name: this.despachosAc[y][z]
                                                    .patient
                                            };
                                            allDespachosAc[
                                                x
                                            ] = this.despachosAc[y][z].quantity;
                                            break;
                                        }
                                    }
                                }
                                if (!match) {
                                    allDespachosAc.push(0);
                                }
                            } */

/* console.log(
                                                this.despachosAc[y].patient +
                                                    " " +
                                                    monthName[
                                                        this.despachosAc[y]
                                                            .groupeddata[z]
                                                            .month - 1
                                                    ] +
                                                    " " +
                                                    this.despachosAc[y]
                                                        .groupeddata[z].year +
                                                    " " +
                                                    this.despachosAc[y]
                                                        .groupeddata[z].quantity
                                            ); */

// --------------------------------------------------------------------------------------------
//Ciclo para transponer datos, validacion entre array meses y array rotacion
//Se asigna 0 donde no haya Despacho

// Itero los pacientes
/*for (var y = 0; y < this.despachosAc.length; y++) {
                                //
                                // Itero los meses
                                for (var x = 0; x < arrayAllMoths.length; x++) {
                                    var match = false;
                                    //
                                    // itero la propiedad Despachos
                                    for (
                                        var z = 0;
                                        z <
                                        this.despachosAc[y].groupeddata.length;
                                        z++
                                    ) {
                                        if (
                                            arrayAllMoths[x] ===
                                            monthName[
                                                this.despachosAc[y].groupeddata[
                                                    z
                                                ].month - 1
                                            ] +
                                                " " +
                                                this.despachosAc[y].groupeddata[
                                                    z
                                                ].year
                                        ) {
                                            match = true;

                                            this.despachosAc[y].groupeddata[
                                                x
                                            ] = {
                                                month: this.despachosAc[y]
                                                    .groupeddata[z].month,
                                                year: this.despachosAc[y]
                                                    .groupeddata[z].year,
                                                quantity: parseInt(
                                                    this.despachosAc[y]
                                                        .groupeddata[z].quantity
                                                )
                                            };

                                            break;
                                        }
                                    }
                                    if (!match) {
                                        this.despachosAc[y].groupeddata[x] = {
                                            month: "",
                                            year: "",
                                            quantity: 0
                                        };
                                    }
                                }
                            }*/
// --------------------------------------------------------------------------------------------

/*for (var x = 0; x < arrayAllMoths.length; x++) {
                                //console.log(arrayAllMoths);
                                var match = false;
                                //
                                // Ciclo para recorrer el array de usuarios
                                for (
                                    var y = 0;
                                    y < this.despachosAc.length;
                                    y++
                                ) {
                                    //
                                    // Ciclo para recorrer el array de despachos
                                    for (
                                        var z = 0;
                                        z < this.despachosAc[y].groupeddata;
                                        z++
                                    ) {
                                        if (
                                            arrayAllMoths[x] ===
                                            monthName[
                                                this.despachosAc[y].groupeddata[
                                                    z
                                                ].month - 1
                                            ] +
                                                " " +
                                                this.despachosAc[y].groupeddata[
                                                    z
                                                ].year
                                        ) {
                                            //console.log(arrayAllMoths[x][y]);
                                            match = true;
                                            //console.log;
                                            this.despachosAc[y].groupeddata[
                                                x
                                            ] = {
                                                month: this.despachosAc[y]
                                                    .groupeddata[z].month,
                                                year: this.despachosAc[y]
                                                    .groupeddata[z].year,
                                                quantity: parseInt(
                                                    this.despachosAc[y]
                                                        .groupeddata[z].quantity
                                                )
                                            };

                                            break;
                                        } else {
                                            console.log("no");
                                        }
                                    }
                                    if (!match) {
                                        this.despachosAc[y].groupeddata[x] = {
                                            month: "",
                                            year: "",
                                            quantity: 0
                                        };
                                    }
                                }
                            }

                            // Asignación de valores del array a la propiedad despachosAcEnd
                            this.despachosAcEnd = this.despachosAc; */

// --------------------------------------------------------------------------------------------

/*  this.despachosAc[y].groupeddata[
                                                z
                                            ] = {
                                                month: this.despachosAc[y]
                                                    .groupeddata[z].month,
                                                year: this.despachosAc[y]
                                                    .groupeddata[z].year,
                                                quantity: parseInt(
                                                    this.despachosAc[y]
                                                        .groupeddata[z].quantity
                                                )
                                            }; */

/* console.log(
                                                "patient" +
                                                    this.despachosAc[y]
                                                        .patient +
                                                    " ---- " +
                                                    this.despachosAc[y]
                                                        .groupeddata[z].month +
                                                    " " +
                                                    this.despachosAc[y]
                                                        .groupeddata[z].year +
                                                    " " +
                                                    this.despachosAc[y]
                                                        .groupeddata[z]
                                                        .quantity +
                                                    " ---- " +
                                                    arrayAllMoths[x]
                                            ); */

// --------------------------------------------------------------------------------------------
/*
for (var y = 0; y < this.despachosAc.length; y++) {
    //
    // Itero los meses
    for (var x = 0; x < arrayAllMoths.length; x++) {
        var match = false;
        //
        // itero la propiedad Despachos
        for (var z = 0; z < this.despachosAc[y].groupeddata.length; z++) {
            if (
                arrayAllMoths[x] ===
                monthName[this.despachosAc[y].groupeddata[z].month - 1] +
                    " " +
                    this.despachosAc[y].groupeddata[z].year
            ) {
                match = true;

                this.despachosAc[y].groupeddata[x] = {
                    month: this.despachosAc[y].groupeddata[z].month,
                    year: this.despachosAc[y].groupeddata[z].year,
                    quantity: parseInt(
                        this.despachosAc[y].groupeddata[z].quantity
                    )
                };

                break;
            }
        }
        if (!match) {
            this.despachosAc[y].groupeddata[x] = {
                month: "",
                year: "",
                quantity: 0
            };
        }
    }
} */

// ----------------------------------------------------------------------------------------------------
/* const arrayMonthsToShow = [...arrayAllMoths]; */
//console.log(arrayMonthsToShow);
// Fin retorno array meses

// Quitando duplicados del array macro de pacientes
/* const arrayWithOutDuplicate = new Set(
                                this.despachosAc.map(item => item.patient)
                            );
                            const arrayData = [...arrayWithOutDuplicate]; */

// ----------------------------------------------------------------------------------------------------

/*for (var z = 0; z < arrayAllMoths.length; z++) {
    var match = false;
    for (var x = 0; x < arrayPaciente.length; x++) {
        var matchPatient = false;
        for (var y = 0; y < arrayData.length; y++) {
            if (arrayData[y] === arrayPaciente[x].patient) {
                matchPatient = true;

                if (
                    arrayAllMoths[z] ===
                    monthName[arrayPaciente[x].month - 1] +
                        " " +
                        arrayPaciente[x].year
                ) {
                    match = true;
                    arrayGroupedData[z] = {
                        patient: arrayPaciente[x].patient,
                        quantity: arrayPaciente[x].quantity
                    };
                    break;
                }
                if (!match) {
                    arrayGroupedData[z] = {
                        quantity: 0
                    };
                }
            }
            arrayPatient[y] = {
                patient: arrayPaciente[y].patient,
                data: arrayGroupedData
            };

            break;
        }
    }
    if (!matchPatient) {
        arrayPatient.push(0);
    }
    arrayEnd = arrayPatient;
}*/

// ---------------------------------------------------------------------------------
/* var arrayPacientes = arrayPatient.map(
                                (item, _indexp) => {
                                    arrayGroupedData = item.groupeddata.map(
                                        (group, _indexg) => {
                                            arrayMonthsToShow = this.monthsArray.map(
                                                (m, _indexm) => {
                                                    if (
                                                        m ===
                                                        monthName[
                                                            group.month - 1
                                                        ] +
                                                            " " +
                                                            group.year
                                                    ) {
                                                        arrayGroup = {
                                                            month: group.month,
                                                            year: group.year,
                                                            quantity:
                                                                group.quantity
                                                        };
                                                    } else {
                                                        arrayGroup = 0;
                                                    }
                                                    return arrayGroup;
                                                }
                                            );
                                        }
                                    );
                                    return {
                                        patient: item.patient,
                                        data: arrayMonthsToShow
                                    };
                                }
                            ); */
