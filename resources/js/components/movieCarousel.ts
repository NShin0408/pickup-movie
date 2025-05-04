export class MovieCarousel {
  private containers: HTMLElement[] = [];
  private isMobile: boolean = window.innerWidth < 768;

  constructor() {
    this.init();
  }

  private init(): void {
    // DOMが完全に読み込まれてからカルーセルを初期化
    setTimeout(() => {
      // カルーセルコンテナを取得
      this.containers = Array.from(document.querySelectorAll('[id$="-carousel"]'));

      // 各カルーセルを初期化
      this.containers.forEach(container => {
        const items = container.querySelector('.flex.transition-transform');
        if (!items) return;

        const itemElements = Array.from(items.querySelectorAll('.flex-none'));
        if (itemElements.length === 0) return;

        // 実際の要素の幅を測定
        const firstItem = itemElements[0] as HTMLElement;
        const itemRect = firstItem.getBoundingClientRect();
        const itemWidth = itemRect.width; // 実際の表示幅を取得

        // PCモードでは約5.2枚表示できるように調整
        const visibleItems = this.isMobile
            ? Math.floor((container.clientWidth - 60) / itemWidth)
            : Math.floor((container.clientWidth - 30) / itemWidth);
        const totalItems = itemElements.length;

        // カルーセルの現在位置
        container.setAttribute('data-position', '0');
        container.setAttribute('data-item-width', itemWidth.toString());

        // PCの場合のみボタンの表示/非表示を設定
        if (!this.isMobile) {
          const nextBtn = container.querySelector('button:nth-child(3)') as HTMLElement;
          const prevBtn = container.querySelector('button:nth-child(2)') as HTMLElement;

          if (totalItems <= visibleItems) {
            // アイテム数が表示可能数以下の場合は両方のボタンを非表示
            if (nextBtn) nextBtn.style.display = 'none';
            if (prevBtn) prevBtn.style.display = 'none';
          } else {
            // 左ボタンは初期状態では非表示（ポジション0のため）
            if (prevBtn) prevBtn.style.display = 'none';
            // 右ボタンは表示
            if (nextBtn) nextBtn.style.display = '';
          }

          // ボタンのイベントリスナーを設定
          if (nextBtn) {
            nextBtn.addEventListener('click', () => this.moveNext(container));
          }

          if (prevBtn) {
            prevBtn.addEventListener('click', () => this.movePrev(container));
          }
        }

        // タッチ操作のサポート（スマートフォン向け）
        this.setupTouchEvents(container, items as HTMLElement);
      });

      // リサイズイベントリスナー
      window.addEventListener('resize', () => {
        this.isMobile = window.innerWidth < 768;
        this.handleResize();
      });
    }, 100); // わずかな遅延を入れてDOMの準備を確実にする
  }

  private setupTouchEvents(container: HTMLElement, itemsContainer: HTMLElement): void {
    let startX: number;
    let currentX: number;
    let isDragging = false;
    let startScrollPosition = 0;

    // タッチイベント（モバイル用）
    container.addEventListener('touchstart', (e) => {
      startX = e.touches[0].clientX;
      currentX = startX;
      startScrollPosition = this.getCurrentScrollPosition(itemsContainer);
      isDragging = true;
    }, { passive: true });

    container.addEventListener('touchmove', (e) => {
      if (!isDragging) return;
      currentX = e.touches[0].clientX;
      const diffX = currentX - startX;

      // スワイプ中の位置を計算して適用
      this.updatePositionOnDrag(itemsContainer, startScrollPosition, diffX);
    }, { passive: true });

    container.addEventListener('touchend', () => {
      if (!isDragging) return;

      // スワイプ終了時、最も近いアイテムにスナップ
      this.snapToNearestItem(container, itemsContainer, startX, currentX);
      isDragging = false;
    });

    // マウスイベント（デスクトップ用）
    if (!this.isMobile) {
      container.addEventListener('mousedown', (e) => {
        e.preventDefault();
        startX = e.clientX;
        currentX = startX;
        startScrollPosition = this.getCurrentScrollPosition(itemsContainer);
        isDragging = true;
        container.style.cursor = 'grabbing';
      });

      window.addEventListener('mousemove', (e) => {
        if (!isDragging) return;
        currentX = e.clientX;
        const diffX = currentX - startX;

        // スワイプ中の位置を計算して適用
        this.updatePositionOnDrag(itemsContainer, startScrollPosition, diffX);
      });

      window.addEventListener('mouseup', () => {
        if (!isDragging) return;
        container.style.cursor = '';

        // スワイプ終了時、最も近いアイテムにスナップ
        this.snapToNearestItem(container, itemsContainer, startX, currentX);
        isDragging = false;
      });
    }
  }

  private getCurrentScrollPosition(itemsContainer: HTMLElement): number {
    const transformValue = itemsContainer.style.transform;
    if (!transformValue || !transformValue.includes('translateX')) return 0;

    const match = transformValue.match(/translateX\(-?(\d+(?:\.\d+)?)px\)/);
    return match ? -parseFloat(match[1]) : 0;
  }

  private updatePositionOnDrag(itemsContainer: HTMLElement, startPosition: number, diffX: number): void {
    const newPosition = startPosition + diffX;
    itemsContainer.style.transform = `translateX(${newPosition}px)`;
  }

  private snapToNearestItem(container: HTMLElement, itemsContainer: HTMLElement, startX: number, endX: number): void {
    const itemWidth = parseFloat(container.getAttribute('data-item-width') || '200');
    const diffX = endX - startX;

    // スワイプ距離に基づいてページネーションを計算
    if (Math.abs(diffX) > 50) { // 一定以上のスワイプ距離でアクション
      let position = parseInt(container.getAttribute('data-position') || '0');
      if (diffX < 0) {
        // 左へスワイプ（次のアイテムグループへ）
        this.moveNext(container);
      } else {
        // 右へスワイプ（前のアイテムグループへ）
        this.movePrev(container);
      }
    } else {
      // スワイプ距離が短い場合は現在位置に戻す
      const position = parseInt(container.getAttribute('data-position') || '0');
      itemsContainer.style.transform = `translateX(-${position * itemWidth}px)`;
    }
  }

  private moveNext(container: HTMLElement): void {
    const items = container.querySelector('.flex.transition-transform') as HTMLElement;
    if (!items) return;

    const itemWidth = parseFloat(container.getAttribute('data-item-width') || '200');
    const itemElements = Array.from(items.querySelectorAll('.flex-none'));
    const totalItems = itemElements.length;
    // PCモードでは約5.2枚表示できるように調整
    const visibleItems = this.isMobile
        ? Math.floor((container.clientWidth - 60) / itemWidth)
        : Math.floor((container.clientWidth - 30) / itemWidth);

    let position = parseInt(container.getAttribute('data-position') || '0');

    // 次のポジションを計算（最大値を超えないようにする）
    position = Math.min(position + visibleItems, totalItems - visibleItems);
    container.setAttribute('data-position', position.toString());

    // カルーセルを移動
    items.style.transform = `translateX(-${position * itemWidth}px)`;

    // ボタンの表示/非表示を更新（PCの場合のみ）
    if (!this.isMobile) {
      this.updateButtonVisibility(container, position, totalItems, visibleItems);
    }
  }

  private movePrev(container: HTMLElement): void {
    const items = container.querySelector('.flex.transition-transform') as HTMLElement;
    if (!items) return;

    const itemWidth = parseFloat(container.getAttribute('data-item-width') || '200');
    const itemElements = Array.from(items.querySelectorAll('.flex-none'));
    const totalItems = itemElements.length;
    // PCモードでは約5.2枚表示できるように調整
    const visibleItems = this.isMobile
        ? Math.floor((container.clientWidth - 60) / itemWidth)
        : Math.floor((container.clientWidth - 30) / itemWidth);

    let position = parseInt(container.getAttribute('data-position') || '0');

    // 前のポジションを計算（0未満にならないようにする）
    position = Math.max(position - visibleItems, 0);
    container.setAttribute('data-position', position.toString());

    // カルーセルを移動
    items.style.transform = `translateX(-${position * itemWidth}px)`;

    // ボタンの表示/非表示を更新（PCの場合のみ）
    if (!this.isMobile) {
      this.updateButtonVisibility(container, position, totalItems, visibleItems);
    }
  }

  private updateButtonVisibility(container: HTMLElement, position: number, totalItems: number, visibleItems: number): void {
    const prevBtn = container.querySelector('button:nth-child(2)') as HTMLElement;
    const nextBtn = container.querySelector('button:nth-child(3)') as HTMLElement;

    if (prevBtn) {
      // 左ボタンはポジションが0より大きい場合のみ表示
      prevBtn.style.display = position > 0 ? 'flex' : 'none';
    }

    if (nextBtn) {
      // 右ボタンは右に移動できる余地がある場合のみ表示
      nextBtn.style.display = position < totalItems - visibleItems ? 'flex' : 'none';
    }
  }

  private handleResize(): void {
    this.containers.forEach(container => {
      const items = container.querySelector('.flex.transition-transform') as HTMLElement;
      if (!items) return;

      const itemElements = Array.from(items.querySelectorAll('.flex-none'));
      if (itemElements.length === 0) return;

      // 実際の要素の幅を再測定
      const firstItem = itemElements[0] as HTMLElement;
      const itemRect = firstItem.getBoundingClientRect();
      const itemWidth = itemRect.width;
      container.setAttribute('data-item-width', itemWidth.toString());

      // PCモードでは約5.2枚表示できるように調整
      const visibleItems = this.isMobile
          ? Math.floor((container.clientWidth - 60) / itemWidth)
          : Math.floor((container.clientWidth - 30) / itemWidth);
      const totalItems = itemElements.length;

      let position = parseInt(container.getAttribute('data-position') || '0');

      // ポジションを再調整
      position = Math.min(position, totalItems - visibleItems);
      position = Math.max(position, 0);
      container.setAttribute('data-position', position.toString());

      // カルーセルを移動
      items.style.transform = `translateX(-${position * itemWidth}px)`;

      // ボタンの表示/非表示を更新（PCの場合のみ）
      if (!this.isMobile) {
        if (totalItems <= visibleItems) {
          // アイテム数が表示可能数以下の場合は両方のボタンを非表示
          const buttons = container.querySelectorAll('button');
          buttons.forEach(button => {
            button.style.display = 'none';
          });
        } else {
          this.updateButtonVisibility(container, position, totalItems, visibleItems);
        }
      }
    });
  }
}
