/*
 * MIT License
 *
 * Copyright (c) 2018-present Marx J. Moura
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

class FileSaveWrapper {
  constructor (file) {
    this._file = file
  }

  save (name, type) {
    name = name || this._file.name
    type = type || this._file.type

    const blob = new Blob([this._file], { type: type })
    const options = { type: blob.type, lastModified: this._file.lastModified }
    const file = new File([blob], name, options)

    this._download(file)
  }

  _download (file) {
    const a = document.createElement('a')

    a.href = URL.createObjectURL(file)
    a.download = file.name

    a.click()
    a.remove()
  }
}

export default FileSaveWrapper
