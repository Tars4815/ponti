/* Annotations definition */
{// Annotation 1
    let Title01 = $(`
                <span>
                    Annotation 1
                </span>
                `);
    let annotation01 = new Potree.Annotation({
        position: [593673.870, 5089120.772, 910.538],
        title: Title01,
        cameraPosition: [593661.279, 5089117.043, 907.581],
        cameraTarget: [593673.870, 5089120.772, 910.538],
        description: 'INSERT DESCRIPTION HERE'
    })
    annotation01.visible = true; // Change this to false if you want to hide the annotations at first loading
    bridgescene.annotations.add(annotation01);
    Title01.toString = () => "Annotation 1";
}
{// Annotation 2
    let Title02 = $(`
                <span>
                    Annotation 2
                </span>
                `);
    let annotation02 = new Potree.Annotation({
        position: [593610.154, 5089104.605, 913.118],
        title: Title02,
        cameraPosition: [593618.588, 5089107.116, 910.433],
        cameraTarget: [593610.154, 5089104.605, 913.118],
        description: 'Visualizza tutte le foto di questo elemento cliccando sul simbolo <a href="https://polimi365-my.sharepoint.com/:f:/g/personal/10462873_polimi_it/EsJ_4IySOmlNuTNgcGmAttkBxqxO7L4y5ZT1NMp0INF4Nw?e=LGGhAT" target="_blank"><img src="./libs/potree/resources/icons/orbit_controls.svg" name="pila1_foto" class="annotation-action-icon" /></a> '
    })
    annotation02.visible = true;
    bridgescene.annotations.add(annotation02);
    Title02.toString = () => "Annotation 2";
}