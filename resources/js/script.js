let openReplyForms = new Set();
let expandedReplies = new Set();

function loadNames() {
    fetch('get_names.php')
        .then(response => response.json())
        .then(data => {
            const nameSelect = document.getElementById('name');
            nameSelect.innerHTML = '';
            data.forEach(user => {
                const option = document.createElement('option');
                option.value = user.name;
                option.textContent = user.name;
                option.dataset.avatar = user.avatar; // 使用data属性存储头像URL
                nameSelect.appendChild(option);
            });
        });
}

let currentPage = 1;
let currentReplyMessageId = null;

function loadMessages(page = 1) {
    fetch(`load_messages.php?page=${page}`)
        .then(response => response.json())
        .then(data => {
            currentPage = page; // 更新当前页码
            const messagesDiv = document.getElementById('messages');
            messagesDiv.innerHTML = '';
            data.messages.forEach(msg => {
                const card = document.createElement('div');
                card.classList.add('shadow', 'card', 'mb-3');
                card.setAttribute('id', `message-${msg.id}`); // 为每条消息设置一个唯一ID
                const cardBody = document.createElement('div');
                cardBody.classList.add('card-body');
                const avatarImg = document.createElement('img');
                avatarImg.src = msg.avatar;
                avatarImg.classList.add('me-3', 'rounded-circle');
                avatarImg.style.width = '50px';
                avatarImg.style.height = '50px';
                const nameStrong = document.createElement('strong');
                nameStrong.textContent = msg.name;
                const messageP = document.createElement('p');
                messageP.classList.add('card-text');
                messageP.textContent = msg.message;
                const timestampSmall = document.createElement('small');
                timestampSmall.classList.add('text-muted');
                timestampSmall.textContent = msg.timestamp;
                const replyButton = document.createElement('button');
                replyButton.classList.add('btn', 'btn-link', 'mt-2');
                replyButton.textContent = '回复';
                replyButton.addEventListener('click', () => showReplyForm(msg.id, msg.name));
                cardBody.appendChild(avatarImg);
                cardBody.appendChild(nameStrong);
                cardBody.appendChild(document.createElement('br'));
                cardBody.appendChild(messageP);
                cardBody.appendChild(timestampSmall);
                cardBody.appendChild(replyButton);
                card.appendChild(cardBody);
                messagesDiv.appendChild(card);

                // 加载回复
                if (msg.replies && msg.replies.length > 0) {
                    const replyDiv = document.createElement('div');
                    replyDiv.classList.add('ms-5');
                    replyDiv.setAttribute('id', `replies-${msg.id}`); // 为每个回复设置唯一ID
                    loadReplies(replyDiv, msg.replies, 1, msg.id);
                    card.appendChild(replyDiv);
                }
            });

            const totalPages = Math.ceil(data.total_messages / data.messages_per_page);
            const paginationDiv = document.getElementById('pagination');
            paginationDiv.innerHTML = '';

            const prevButton = document.createElement('button');
            prevButton.classList.add('btn', 'btn-secondary', 'me-2');
            prevButton.textContent = '上一页';
            prevButton.disabled = data.current_page === 1;
            prevButton.addEventListener('click', () => loadMessages(data.current_page - 1));
            paginationDiv.appendChild(prevButton);

            const pageIndicator = document.createElement('span');
            pageIndicator.textContent = `第 ${data.current_page} 页，共 ${totalPages} 页`;
            paginationDiv.appendChild(pageIndicator);

            const nextButton = document.createElement('button');
            nextButton.classList.add('btn', 'btn-secondary', 'ms-2');
            nextButton.textContent = '下一页';
            nextButton.disabled = data.current_page === totalPages;
            nextButton.addEventListener('click', () => loadMessages(data.current_page + 1));
            paginationDiv.appendChild(nextButton);

            const pageInput = document.createElement('input');
            pageInput.type = 'number';
            pageInput.classList.add('form-control', 'ms-2');
            pageInput.style.width = '60px';
            pageInput.min = 1;
            pageInput.max = totalPages;
            pageInput.value = data.current_page;
            paginationDiv.appendChild(pageInput);

            const jumpButton = document.createElement('button');
            jumpButton.classList.add('btn', 'btn-primary', 'ms-2');
            jumpButton.textContent = '跳转';
            jumpButton.addEventListener('click', () => {
                let newPage = parseInt(pageInput.value);
                if (newPage >= 1 && newPage <= totalPages) {
                    loadMessages(newPage);
                } else {
                    pageInput.value = data.current_page;
                }
            });
            paginationDiv.appendChild(jumpButton);
        });
}

function loadReplies(container, replies, level, parentId) {
    const visibleReplies = replies.slice(0, 2);
    const hiddenReplies = replies.slice(2);

    visibleReplies.forEach(reply => {
        const card = createReplyCard(reply, level);
        container.appendChild(card);
    });

    if (hiddenReplies.length > 0) {
        const collapseDiv = document.createElement('div');
        collapseDiv.classList.add('collapse');
        collapseDiv.id = `collapseReplies-${parentId}`;

        hiddenReplies.forEach(reply => {
            const card = createReplyCard(reply, level);
            collapseDiv.appendChild(card);
        });

        container.appendChild(collapseDiv);

        const showMoreButton = document.createElement('button');
        showMoreButton.classList.add('btn', 'btn-link', 'mt-2');
        showMoreButton.textContent = '展开/折叠更多回复';
        showMoreButton.setAttribute('data-bs-toggle', 'collapse');
        showMoreButton.setAttribute('data-bs-target', `#collapseReplies-${parentId}`);
        container.appendChild(showMoreButton);

        // Restore the collapse state from localStorage
        if (expandedReplies.has(parentId)) {
            const bootstrapCollapse = new bootstrap.Collapse(collapseDiv, {
                toggle: false
            });
            bootstrapCollapse.show();
        }

        // Save the collapse state to localStorage
        collapseDiv.addEventListener('shown.bs.collapse', () => {
            expandedReplies.add(parentId);
            localStorage.setItem('expandedReplies', JSON.stringify([...expandedReplies]));
        });

        collapseDiv.addEventListener('hidden.bs.collapse', () => {
            expandedReplies.delete(parentId);
            localStorage.setItem('expandedReplies', JSON.stringify([...expandedReplies]));
        });
    }
}

function createReplyCard(reply, level) {
    const card = document.createElement('div');
    card.classList.add('shadow', 'card', 'mb-3');
    const cardBody = document.createElement('div');
    cardBody.classList.add('card-body');
    const avatarImg = document.createElement('img');
    avatarImg.src = reply.avatar;
    avatarImg.classList.add('me-3', 'rounded-circle');
    avatarImg.style.width = '40px';
    avatarImg.style.height = '40px';
    const nameStrong = document.createElement('strong');
    nameStrong.textContent = reply.name;
    const messageP = document.createElement('p');
    messageP.classList.add('card-text');
    messageP.textContent = reply.message;
    const timestampSmall = document.createElement('small');
    timestampSmall.classList.add('text-muted');
    timestampSmall.textContent = reply.timestamp;
    cardBody.appendChild(avatarImg);
    cardBody.appendChild(nameStrong);
    cardBody.appendChild(document.createElement('br'));
    cardBody.appendChild(messageP);
    cardBody.appendChild(timestampSmall);
    card.appendChild(cardBody);
    return card;
}

function showReplyForm(messageId, replyToName) {
    currentReplyMessageId = messageId;
    document.getElementById('replyModalLabel').textContent = `回复 ${replyToName}`;

    const replyModal = new bootstrap.Modal(document.getElementById('replyModal'));
    replyModal.show();

    function loadReplyNames() {
        fetch('get_names.php')
            .then(response => response.json())
            .then(data => {
                const replyNameSelect = document.getElementById('replyName');
                replyNameSelect.innerHTML = '';
                data.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.name;
                    option.textContent = user.name;
                    option.dataset.avatar = user.avatar; // 使用data属性存储头像URL
                    replyNameSelect.appendChild(option);
                });
            });
    }

    loadReplyNames(); // 加载回复名称列表

    document.getElementById('replyNameSearch').addEventListener('input', function(event) {
        const searchTerm = event.target.value.toLowerCase();
        const replyNameSelect = document.getElementById('replyName');
        fetch('get_names.php')
            .then(response => response.json())
            .then(data => {
                replyNameSelect.innerHTML = '';
                data.forEach(user => {
                    if (user.name.toLowerCase().includes(searchTerm)) {
                        const option = document.createElement('option');
                        option.value = user.name;
                        option.textContent = user.name;
                        option.dataset.avatar = user.avatar;
                        replyNameSelect.appendChild(option);
                    }
                });
            });
    });

    const replyForm = document.getElementById('replyForm');
    replyForm.removeEventListener('submit', submitReplyForm); // 确保没有重复绑定
    replyForm.addEventListener('submit', submitReplyForm);
}

function submitReplyForm(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const replyNameSelect = document.getElementById('replyName');
    const selectedOption = replyNameSelect.options[replyNameSelect.selectedIndex];
    const avatar = selectedOption.dataset.avatar;

    formData.append('avatar', avatar);
    formData.append('parent_id', currentReplyMessageId);

    fetch('post_reply.php', {
        method: 'POST',
        body: formData
    }).then(response => response.text())
        .then(result => {
            showAlert(result); // 显示服务器返回的消息
            loadMessages(currentPage); // 重新加载当前页消息
            const replyModal = bootstrap.Modal.getInstance(document.getElementById('replyModal'));
            replyModal.hide(); // 关闭回复modal
        })
        .catch(error => {
            console.error('Error posting reply:', error);
        });
}

function showAlert(message) {
    alert(message);
}

document.getElementById('messageForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const nameSelect = document.getElementById('name');
    const selectedOption = nameSelect.options[nameSelect.selectedIndex];
    const avatar = selectedOption.dataset.avatar;

    formData.append('avatar', avatar);

    fetch('post_message.php', {
        method: 'POST',
        body: formData
    }).then(response => response.text())
        .then(result => {
            showAlert('发送成功！');
            loadMessages(); // 立即加载新消息
            event.target.reset();
        })
        .catch(error => {
            console.error('Error posting message:', error);
        });
});

document.getElementById('nameSearch').addEventListener('input', function(event) {
    const searchTerm = event.target.value.toLowerCase();
    const nameSelect = document.getElementById('name');
    fetch('get_names.php')
        .then(response => response.json())
        .then(data => {
            nameSelect.innerHTML = '';
            data.forEach(user => {
                if (user.name.toLowerCase().includes(searchTerm)) {
                    const option = document.createElement('option');
                    option.value = user.name;
                    option.textContent = user.name;
                    option.dataset.avatar = user.avatar;
                    nameSelect.appendChild(option);
                }
            });
        });
});

loadNames(); // 页面加载时加载名称列表
loadMessages(currentPage); // 页面加载时加载第一页的消息
expandedReplies = new Set(JSON.parse(localStorage.getItem('expandedReplies')) || []);
setInterval(() => {
    if (document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
        loadMessages(currentPage); // 只有在用户没有输入时才加载
    }
}, 5000); // 每5秒加载一次当前页的留言
