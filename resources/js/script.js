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
            currentPage = page;
            const messagesDiv = document.getElementById('messages');
            messagesDiv.innerHTML = '';
            data.messages.forEach(msg => {
                const card = document.createElement('div');
                card.classList.add('shadow', 'card', 'mb-2');
                card.setAttribute('id', `message-${msg.id}`);

                const cardBody = document.createElement('div');
                cardBody.classList.add('card-body', 'position-relative');

                const avatarImg = document.createElement('img');
                avatarImg.src = msg.avatar;
                avatarImg.classList.add('me-3', 'rounded-circle');
                avatarImg.style.width = '50px';
                avatarImg.style.height = '50px';

                const nameStrong = document.createElement('strong');
                nameStrong.textContent = msg.name;

                const moodImg = document.createElement('img');
                moodImg.src = getMoodImageUrl(msg.mood);
                moodImg.classList.add('ms-2');
                moodImg.style.width = '55px';
                moodImg.style.height = '20px';

                const messageP = document.createElement('p');
                messageP.classList.add('card-text');
                messageP.textContent = msg.message;

                const timestampSmall = document.createElement('small');
                timestampSmall.classList.add('text-muted');
                timestampSmall.textContent = msg.timestamp;

                const cardFooter = document.createElement('div');
                cardFooter.classList.add('card-footer');
                const messageIdSpan = document.createElement('span');
                messageIdSpan.textContent = `ID: ${msg.id}`;
                cardFooter.appendChild(timestampSmall);
                cardFooter.appendChild(messageIdSpan);

                const replyButton = document.createElement('button');
                replyButton.classList.add('btn', 'btn-link', 'position-absolute', 'top-0', 'end-0');
                const replyIcon = document.createElement('i');
                replyIcon.classList.add('bi', 'bi-reply-fill');
                replyButton.appendChild(replyIcon);
                replyButton.addEventListener('click', () => showReplyForm(msg.id, msg.name));

                cardBody.appendChild(avatarImg);
                cardBody.appendChild(nameStrong);
                cardBody.appendChild(moodImg);
                cardBody.appendChild(messageP);
                cardBody.appendChild(replyButton);
                card.appendChild(cardBody);
                card.appendChild(cardFooter);
                messagesDiv.appendChild(card);

                if (msg.replies && msg.replies.length > 0) {
                    const replyDiv = document.createElement('div');
                    replyDiv.classList.add('ms-4', 'ms-md-5');
                    replyDiv.setAttribute('id', `replies-${msg.id}`);
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

function getMoodImageUrl(mood) {
    switch (mood) {
        case '绝不调':
            return './resources/img/mood/1.png';
        case '不调':
            return './resources/img/mood/2.png';
        case '普通':
            return './resources/img/mood/3.png';
        case '好调':
            return './resources/img/mood/4.png';
        case '绝好调':
            return './resources/img/mood/5.png';
        default:
            return './resources/img/mood/3.png';
    }
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

    const cardFooter = document.createElement('div');
    cardFooter.classList.add('card-footer');
    const replyIdSpan = document.createElement('span');
    replyIdSpan.textContent = `ID: ${reply.id}`;
    cardFooter.appendChild(timestampSmall);
    cardFooter.appendChild(replyIdSpan);

    cardBody.appendChild(avatarImg);
    cardBody.appendChild(nameStrong);
    cardBody.appendChild(messageP);
    card.appendChild(cardBody);
    card.appendChild(cardFooter);
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

    document.getElementById('replyMessage').addEventListener('input', updateReplyCharacterCount);
}

function submitReplyForm(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const replyNameSelect = document.getElementById('replyName');
    const selectedOption = replyNameSelect.options[replyNameSelect.selectedIndex];
    const avatar = selectedOption.dataset.avatar;
    const replyMessage = document.getElementById('replyMessage').value;

    if (replyMessage.trim() === "") {
        showToast('请输入回复内容！');
        return;
    }

    if (replyMessage.length > 100) {
        showToast('回复内容不能超过100字！');
        return;
    }

    formData.append('avatar', avatar);
    formData.append('parent_id', currentReplyMessageId);

    fetch('post_reply.php', {
        method: 'POST',
        body: formData
    }).then(response => response.text())
        .then(result => {
            showToast('成功回复啦！！');
            loadMessages(currentPage);
            const replyModal = bootstrap.Modal.getInstance(document.getElementById('replyModal'));
            replyModal.hide();
        })
        .catch(error => {
            console.error('Error posting reply:', error);
            showToast('啊！？回复失败了！？');
        });
}

function showToast(message) {
    const toastMessage = document.getElementById('toastMessage');
    toastMessage.textContent = message;

    const toastTime = document.getElementById('toastTime');
    const now = new Date();
    toastTime.textContent = now.toLocaleTimeString();

    const toastElement = document.getElementById('liveToast');
    const toast = new bootstrap.Toast(toastElement);
    toast.show();
}

document.getElementById('messageForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const nameSelect = document.getElementById('name');
    const selectedOption = nameSelect.options[nameSelect.selectedIndex];
    const avatar = selectedOption.dataset.avatar;
    const messageContent = document.getElementById('message').value;

    if (messageContent.trim() === "") {
        showToast('请输入留言内容！');
        return;
    }

    if (messageContent.length > 100) {
        showToast('留言内容不能超过100字！');
        return;
    }

    formData.append('avatar', avatar);

    fetch('post_message.php', {
        method: 'POST',
        body: formData
    }).then(response => response.text())
        .then(result => {
            showToast('成功发送新的留言！！');
            loadMessages(); // 立即加载新消息
            event.target.reset();
            updateMessageCharacterCount();
        })
        .catch(error => {
            console.error('Error posting message:', error);
            showToast('草！发送失败了！');
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

document.getElementById('message').addEventListener('input', updateMessageCharacterCount);

function updateMessageCharacterCount() {
    const messageInput = document.getElementById('message');
    const messageCount = document.getElementById('messageCount');
    messageCount.textContent = `${messageInput.value.length}/100`;
}

function updateReplyCharacterCount() {
    const replyInput = document.getElementById('replyMessage');
    const replyCount = document.getElementById('replyCount');
    if (replyCount) {
        replyCount.textContent = `${replyInput.value.length}/100`;
    } else {
        const replyCountDiv = document.createElement('div');
        replyCountDiv.classList.add('text-end', 'text-muted');
        replyCountDiv.id = 'replyCount';
        replyCountDiv.textContent = `${replyInput.value.length}/100`;
        replyInput.parentElement.appendChild(replyCountDiv);
    }
}

loadNames(); // 页面加载时加载名称列表
loadMessages(currentPage); // 页面加载时加载第一页的消息
expandedReplies = new Set(JSON.parse(localStorage.getItem('expandedReplies')) || []);
setInterval(() => {
    if (document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
        loadMessages(currentPage); // 只有在用户没有输入时才加载
    }
}, 5000); // 每5秒加载一次当前页的留言

// 添加发布新留言的卡片样式
const messageFormCard = document.createElement('div');
messageFormCard.classList.add('shadow', 'card', 'mb-3');
const messageFormCardBody = document.createElement('div');
messageFormCardBody.classList.add('card-body');
const messageForm = document.getElementById('messageForm');
messageForm.classList.add('mb-3');
messageFormCardBody.appendChild(messageForm);
messageFormCard.appendChild(messageFormCardBody);
document.getElementById('newMessageCard').appendChild(messageFormCard);
