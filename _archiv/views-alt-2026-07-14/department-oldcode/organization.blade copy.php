<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizational Chart</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .org-chart {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 2rem;
            position: relative;
        }

        .dept-block {
            position: relative;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1rem;
            background-color: #f9f9f9;
            text-align: center;
            min-width: 150px;
        }

        .dept-title {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .child-departments {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            margin-top: 2rem;
            gap: 2rem;
            position: relative;
        }

        /* Vertical line connecting a node to its parent */
        .line-vertical {
            position: absolute;
            width: 2px;
            background-color: #000;
            top: 0;
            left: 50%;
            z-index: -1;
        }

        /* Horizontal line connecting sibling nodes */
        .line-horizontal {
            position: absolute;
            height: 2px;
            background-color: #000;
            top: 0;
            z-index: -1;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div id="orgChart" class="org-chart">
        <!-- Top-Level Department -->
        <div class="dept-block" data-dept-id="1" id="dept-1">
            <div class="dept-title">SOLAR ASPEKT</div>
            <div class="add-btn" onclick="addSubDepartment(1)">+ Add Sub-department</div>
            <div class="child-departments" id="children-1"></div>
        </div>
    </div>
</div>

<script>
    let departmentIdCounter = 2; // Initial ID for sub-departments

    function addSubDepartment(parentId) {
        const parentDiv = document.getElementById(`children-${parentId}`);
        const newDeptId = departmentIdCounter++;

        // Create sub-department block
        const newDeptBlock = document.createElement('div');
        newDeptBlock.classList.add('dept-block');
        newDeptBlock.setAttribute('data-dept-id', newDeptId);
        newDeptBlock.id = `dept-${newDeptId}`;
        newDeptBlock.innerHTML = `
            <div class="dept-title">New Department ${newDeptId}</div>
            <div class="add-btn" onclick="addSubDepartment(${newDeptId})">+ Add Sub-department</div>
            <div class="child-departments" id="children-${newDeptId}"></div>
        `;

        // Append to parent node
        parentDiv.appendChild(newDeptBlock);

        // Draw connecting lines
        drawConnectingLines(parentDiv, newDeptBlock, parentId);
    }

    function drawConnectingLines(parentDiv, childDiv, parentId) {
        const parentNode = document.getElementById(`dept-${parentId}`);
        const parentRect = parentNode.getBoundingClientRect();
        const childRect = childDiv.getBoundingClientRect();

        // Vertical line
        const verticalLine = document.createElement('div');
        verticalLine.classList.add('line-vertical');
        verticalLine.style.height = `${childRect.top - parentRect.bottom}px`;
        childDiv.appendChild(verticalLine);

        // Horizontal lines if siblings exist
        if (parentDiv.children.length > 1) {
            const siblings = Array.from(parentDiv.children);
            const firstSiblingRect = siblings[0].getBoundingClientRect();
            const lastSiblingRect = siblings[siblings.length - 1].getBoundingClientRect();

            const horizontalLine = document.createElement('div');
            horizontalLine.classList.add('line-horizontal');
            horizontalLine.style.width = `${lastSiblingRect.right - firstSiblingRect.left}px`;
            horizontalLine.style.left = `${firstSiblingRect.left - childRect.left}px`;
            horizontalLine.style.top = `${childRect.top - parentRect.bottom - 15}px`;

            parentDiv.appendChild(horizontalLine);
        }
    }
</script>
</body>
</html>
