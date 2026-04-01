<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Organization Chart</title>
    <style>
        :root {
            --line-color: #000;
            --node-bg: #8fc73e;
            --text-color: white;
        }

        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            text-align: center;
            background: #f5f5f5;
            padding: 20px;
        }

        .tree {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        .node {
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--node-bg);
            color: var(--text-color);
            padding: 10px 20px;
            border-radius: 5px;
            position: relative;
            margin: 10px auto;
        }

        .node-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        .children {
            display: flex;
            justify-content: center;
            position: relative;
        }

        .line {
            width: 2px;
            background: var(--line-color);
            position: absolute;
        }

        .vertical-line {
            height: 20px;
            left: 50%;
            transform: translateX(-50%);
        }

        .horizontal-line {
            width: 50%;
            height: 2px;
            top: 0;
        }

        .add-button {
            margin-top: 5px;
            cursor: pointer;
            background: white;
            border: 1px solid #000;
            padding: 5px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="tree" id="tree">
        <div class="node-container" id="root">
            <div class="node">CEO</div>
            <button class="add-button" onclick="addNode('root')">+ Add Level</button>
        </div>
    </div>

    <script>
        function addNode(parentId) {
            let parentContainer = document.getElementById(parentId);
            let tree = document.getElementById("tree");

            let childrenContainer = parentContainer.querySelector(".children");
            if (!childrenContainer) {
                childrenContainer = document.createElement("div");
                childrenContainer.className = "children";
                parentContainer.appendChild(childrenContainer);
            }

            let newNodeContainer = document.createElement("div");
            newNodeContainer.className = "node-container";
            let newNodeId = `node-${Date.now()}`;
            newNodeContainer.id = newNodeId;

            let newNode = document.createElement("div");
            newNode.className = "node";
            newNode.innerText = "New Level";

            let addButton = document.createElement("button");
            addButton.className = "add-button";
            addButton.innerText = "+ Add Level";
            addButton.onclick = () => addNode(newNodeId);

            let verticalLine = document.createElement("div");
            verticalLine.className = "line vertical-line";

            newNodeContainer.appendChild(verticalLine);
            newNodeContainer.appendChild(newNode);
            newNodeContainer.appendChild(addButton);
            childrenContainer.appendChild(newNodeContainer);

            let siblings = childrenContainer.children;
            if (siblings.length > 1) {
                let horizontalLine = document.createElement("div");
                horizontalLine.className = "line horizontal-line";
                childrenContainer.insertBefore(horizontalLine, newNodeContainer);
            }
        }
    </script>
</body>
</html>